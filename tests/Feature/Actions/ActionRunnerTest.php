<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use SteelAnts\LaravelBoilerplate\Support\Actions\Action;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryActions;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryFixture;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__ . '/../../Fixtures/ActionsFixture.php';

// Akce nad objektem, který není model (záznam logu) - vyhodnocení i spuštění přes ActionRunner.
// Gate s nepovinným $user se vyhodnotí i bez přihlášeného uživatele.

beforeEach(function () {
    LogEntryFixture::$store = [];
    $this->allowed = true;
    Gate::define('resolve-log', fn ($user = null) => $this->allowed);
    $this->runner = app(ActionRunner::class);
    $this->set = app(LogEntryActions::class);
});

test('items carry the presentation evaluated for the subject', function () {
    $items = $this->runner->items($this->set, new LogEntryFixture('a1'));

    expect(array_map(fn ($item) => $item->name, $items))->toBe(['show', 'resolve', 'remove', 'inspect'])
        ->and($items[0]->url)->toBe('/logs/a1')
        ->and($items[0]->isLink())->toBeTrue()
        ->and($items[1]->text)->toBe('Resolve a1')
        ->and($items[1]->confirm)->toBe('Sure?')
        ->and($items[2]->iconClass())->toBe('fas fa-trash text-danger')
        ->and($items[2]->actionClass())->toBe('text-danger')
        ->and($items[3]->isLivewire())->toBeTrue()
        ->and($items[3]->method)->toBe('inspect')
        ->and($items[3]->parameters)->toBe('a1');
});

test('an unlisted action is not offered but can be run', function () {
    $entry = new LogEntryFixture('a1');

    expect(array_map(fn ($item) => $item->name, $this->runner->items($this->set, $entry)))->not->toContain('toggleResolved');

    $this->runner->run($this->set, $entry, 'toggleResolved');

    expect($entry->resolved)->toBeTrue();
});

test('run calls the handler with the subject and services from the container', function () {
    $entry = new LogEntryFixture('a1');

    $result = $this->runner->run($this->set, $entry, 'resolve');

    expect($entry->resolved)->toBeTrue()
        ->and($result->redirect)->toBeNull()
        ->and($result->removed)->toBeFalse();
});

test('an action hidden by state is neither offered nor runnable', function () {
    $entry = new LogEntryFixture('a1', resolved: true);

    expect(array_map(fn ($item) => $item->name, $this->runner->items($this->set, $entry)))->toBe(['show', 'remove', 'inspect'])
        ->and(fn () => $this->runner->run($this->set, $entry, 'resolve'))->toThrow(AuthorizationException::class);
});

test('an action denied by its ability is neither offered nor runnable', function () {
    $this->allowed = false;
    $entry = new LogEntryFixture('a1');

    expect(array_map(fn ($item) => $item->name, $this->runner->items($this->set, $entry)))->not->toContain('resolve')
        ->and(fn () => $this->runner->run($this->set, $entry, 'resolve'))->toThrow(AuthorizationException::class)
        ->and($entry->resolved)->toBeFalse();
});

test('remove() runs its handler and reports the subject as removed', function () {
    $entry = new LogEntryFixture('a1');

    expect($this->runner->run($this->set, $entry, 'remove')->removed)->toBeTrue()
        ->and(LogEntryFixture::$store)->toBe([]);
});

test('a link, a livewire action or an unknown action cannot be run', function () {
    $entry = new LogEntryFixture('a1');

    expect(fn () => $this->runner->run($this->set, $entry, 'show'))->toThrow(NotFoundHttpException::class)
        ->and(fn () => $this->runner->run($this->set, $entry, 'inspect'))->toThrow(NotFoundHttpException::class)
        ->and(fn () => $this->runner->run($this->set, $entry, 'nope'))->toThrow(NotFoundHttpException::class);
});

test('a set rejects duplicate names and actions without exactly one of url, livewire or handler', function (array $actions) {
    $set = new class ($actions) extends ActionSet
    {
        public function __construct(private array $definitions)
        {
        }

        protected function define(): array
        {
            return $this->definitions;
        }

        public function resolve(string $key): object
        {
            return new stdClass();
        }

        public function key(object $subject): string
        {
            return '';
        }
    };

    $set->actions();
})->with([
    'duplicate'  => fn () => [Action::make('a')->url('/'), Action::make('a')->url('/')],
    'no handler' => fn () => [Action::make('a')],
    'both'       => fn () => [Action::make('a')->url('/')->handle(fn () => null)],
    'livewire'   => fn () => [Action::make('a')->livewire('edit')->handle(fn () => null)],
])->throws(LogicException::class);
