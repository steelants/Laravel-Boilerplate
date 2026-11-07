# Actions

Actions define, in one place, everything a user can do with an object: links (Edit, Show) and operations
(Mark as paid, Unlock, Remove). The same definition feeds the DataTable row menu, the actions menu on a detail
page and bulk actions over a DataTable selection, so labels, confirmations and permissions cannot drift apart.

The subject does not have to be an Eloquent model - logs, events or DTOs work too.

## Action sets

An action set lists the actions of one subject type. For Eloquent models extend `ModelActionSet`:

```php
use SteelAnts\LaravelBoilerplate\Support\Actions\Action;
use SteelAnts\LaravelBoilerplate\Support\Actions\ModelActionSet;

class InvoiceActions extends ModelActionSet
{
    protected string $model = Invoice::class;

    protected function define(): array
    {
        return [
            Action::make('edit')
                ->label(__('Edit'))
                ->icon('fas fa-pen')
                ->url(fn (Invoice $invoice) => route('invoice.edit', $invoice))
                ->can('update'),

            Action::make('unlock')
                ->label(__('Unlock'))
                ->icon('fas fa-unlock')
                ->confirm(__('Are you sure you want to unlock this invoice?'))
                ->visible(fn (Invoice $invoice) => $invoice->is_locked)
                ->can('has-manager')
                ->handle(fn (Invoice $invoice, InvoiceService $service) => $service->unlock($invoice)),

            Action::delete()->confirm(__('Are you sure you want to remove this invoice?')),
        ];
    }

    public function indexUrl(object $subject): string
    {
        return route('invoice.index');
    }
}
```

- An action is exactly one of: a link (`url()`), an operation (`handle()`) or a Livewire component method
  (`livewire('edit')`, e.g. opening a modal - offered in the DataTable only, the detail menu skips it).
  The method receives the subject key, or the value of the second argument: `livewire('trace', fn (FailedJob $job) => $job->uuid)`.
- Any value can be a closure. The container calls it with the subject (matched by its class or by the parameter
  name `$subject`) and injects services, e.g. `fn (Invoice $invoice, InvoiceService $service) => ...`.
- `visible()` checks the subject state, `can()` checks a policy ability or gate. Both are evaluated when the menu
  is rendered **and** again when the action runs, so a hidden action cannot be called from the browser.
- `handle()` may return an `ActionResult`: `refresh()` (default), `redirect($url)` or `removed()`.
- `Action::delete()` removes a model through its `delete` policy and returns `ActionResult::removed()`.
- `Action::remove(fn (Supplier $supplier, SupplierService $service) => $service->delete($supplier))` is the same
  "Remove" action handled by your own service (which authorizes itself); it also returns `ActionResult::removed()`.
- `unlisted()` keeps an action runnable but hides it from menus - typically a boolean column toggled by
  `RenderCasts\ToggleAction` (`is_active` calls the action `toggleIsActive`).

For other subjects extend `ActionSet` and implement `resolve(string $key): object` and `key(object $subject): string`.
`resolve()` loads the subject from a key coming from the browser, so it must restrict it to what the user may access.

## DataTable row actions

```php
use SteelAnts\LaravelBoilerplate\Attributes\UseActions;
use SteelAnts\LaravelBoilerplate\Traits\WithActions;

#[UseActions(InvoiceActions::class)]
class DataTable extends DataTableComponent
{
    use UseDatabase, WithActions;
}
```

`WithActions` implements `actions($row)` and `runAction()`. Actions are evaluated against the model, not against
the row array: the models of the whole page are loaded with a single query (`resolveMany()`).

React to a finished action (alert, event for other components) in the `afterAction()` hook. A table showing different
subjects can pick the set at runtime by overriding `actionSet()`:

```php
protected function afterAction(string $name, ActionResult $result): void
{
    $this->dispatch('milestoneUpdated');
}

protected function actionSet(): ActionSet
{
    return app($this->failed ? FailedJobActions::class : JobActions::class);
}
```

A boolean column can toggle an unlisted action:

```php
public function renderCasts(): array
{
    return ['is_active' => ToggleAction::class]; // runs the action "toggleIsActive"
}
```

## Detail page menu

Put `#[UseActions]` on the subject class and render the Livewire menu:

```php
#[UseActions(InvoiceActions::class)]
class Invoice extends Model
```

```blade
<livewire:boilerplate.actions.menu :subject="$invoice" />
```

A subject without the attribute can pass the set explicitly: `:set="LogEntryActions::class"`.
`:as-button="true"` renders the actions as buttons instead of a dropdown.
After an action the menu reloads the page; after `ActionResult::removed()` it goes to `indexUrl()`.

## Bulk actions

Bulk actions live in a separate set. Their input is the list of selected keys:

```php
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkAction;
use SteelAnts\LaravelBoilerplate\Support\Actions\ModelBulkActionSet;

class InvoiceBulkActions extends ModelBulkActionSet
{
    protected string $model = Invoice::class;

    protected function define(): array
    {
        return [
            BulkAction::make('markPaid')
                ->label(__('Mark as paid'))
                ->icon('fas fa-check')
                ->authorize('has-manager')
                ->can('update')
                ->applicable(fn (Invoice $invoice) => !$invoice->is_paid, __('Already paid'))
                ->each(fn (Invoice $invoice, InvoiceService $service) => $service->setPaid($invoice, true)),

            BulkAction::delete()->authorize('has-manager'),
        ];
    }
}
```

```php
#[UseActions(InvoiceActions::class)]
#[UseBulkActions(InvoiceBulkActions::class)]
class DataTable extends DataTableComponent
{
    use UseDatabase, WithActions, WithBulkActions;
}
```

- `authorize()` is a gate without a subject and decides whether the action is offered at all. Selection is enabled
  only when at least one bulk action is offered.
- `can()` and `applicable()` are checked for every selected object; objects that fail are skipped with a reason.
- `each()` handles objects one by one: an `AuthorizationException` or `ActionException` skips the object with a
  reason, any other exception marks it as failed (reported) and processing continues.
- `handle(fn (Collection $subjects, ...) => ...)` receives all objects that passed at once (export, ...).
  It may return a `BulkResult`, otherwise all passed objects count as processed.
- `keys(fn (array $keys, InvoiceService $service) => $service->markPaid($keys))` loads nothing: it passes the list of
  selected keys (the parameter must be named `$keys`) straight to your service, which does the work - for example
  one mass update. The returned number is reported as processed (default: the number of keys), a returned
  `BulkResult` is passed through. It cannot be combined with `can()` or `applicable()`.
- For `each()` and `handle()` the selection is restricted to the DataTable `query()` (`selectedQuery()`), forged keys
  are ignored. `keys()` receives the selection as sent by the browser, so **the service must restrict the keys** to
  records the user may change (tenant, ownership, state).
- The result is shown as an alert, e.g. "Processed 0 of 2 items. 2 skipped. Already paid (2)".

The DataTable view must render the bulk bar and the selection column
(`<x-datatable-bulk-bar ...>`, `:$selectable` on the head and body components).

## Running actions from code

The `Actions` facade gives access to the `ActionRunner` that the DataTable and the menu use. Use it to run an
action from a controller, service or command. Visibility and abilities are checked the same way as in the menu:

```php
use SteelAnts\LaravelBoilerplate\Facades\Actions;

Actions::run(app(ProjectActions::class), $project, 'archive');          // ActionResult
Actions::items(app(OrderActions::class), $order);                       // list<ActionItem> offered to the user
Actions::runBulk(app(InvoiceBulkActions::class), $keys, 'markPaid');    // BulkResult
```

In tests the facade can be mocked: `Actions::shouldReceive('run')->once()`. Classes that already use dependency
injection can type-hint `ActionRunner` instead.

## Errors

Throw `SteelAnts\LaravelBoilerplate\Exceptions\ActionException` from a service to refuse an operation with a
message for the user (for example "The default supplier cannot be removed"). The `HandlesActionErrors` trait used by
`WithActions`, `WithBulkActions` and the menu turns it, as well as `AuthorizationException`, into an alert instead of
an error page. It can be used on its own:

```php
use SteelAnts\LaravelBoilerplate\Traits\HandlesActionErrors;

if ($this->attempt(fn () => $service->archive($project))) {
    // success
}
```
