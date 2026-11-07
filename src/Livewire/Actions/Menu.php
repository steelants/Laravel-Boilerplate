<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Actions;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use LogicException;
use SteelAnts\LaravelBoilerplate\Attributes\UseActions;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionItem;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet;
use SteelAnts\LaravelBoilerplate\Traits\HandlesActionErrors;

/**
 * Menu akcí objektu v detailu: <livewire:boilerplate.actions.menu :subject="$invoice" />.
 * Bere akce ze stejné sady (#[UseActions] na třídě objektu) a spouští je stejným ActionRunnerem
 * jako řádek DataTable, takže se nabídka, oprávnění ani chování nemohou rozejít.
 */
class Menu extends Component
{
    use HandlesActionErrors;

    /** @var class-string<ActionSet> */
    #[Locked]
    public string $set;

    #[Locked]
    public string $key;

    /** Stránka detailu - vykreslená na serveru, po akci ji proto načteme znovu. */
    #[Locked]
    public string $pageUrl;

    /** Tlačítka vedle sebe místo dropdown menu (např. hlavička stránky v nastavení). */
    #[Locked]
    public bool $asButton = false;

    /** @param  class-string<ActionSet>|null  $set  sada pro objekty bez atributu #[UseActions] */
    public function mount(object $subject, ?string $set = null, bool $asButton = false): void
    {
        $this->asButton = $asButton;
        $this->set = $set ?? UseActions::on($subject) ?? throw new LogicException($subject::class . ' needs the #[UseActions(...)] attribute.');
        $this->key = $this->actionSet()->key($subject);
        $this->pageUrl = url()->current();
    }

    #[Computed]
    public function subject(): object
    {
        return $this->actionSet()->resolve($this->key);
    }

    /** @return list<ActionItem> akce kromě Livewire akcí - ty volají metodu DataTable, kterou detail nemá */
    #[Computed]
    public function items(): array
    {
        return array_values(array_filter(
            app(ActionRunner::class)->items($this->actionSet(), $this->subject),
            fn (ActionItem $item) => !$item->isLivewire(),
        ));
    }

    public function run(string $name): void
    {
        $set = $this->actionSet();
        $subject = $this->subject;

        $this->attempt(function () use ($set, $subject, $name) {
            $result = app(ActionRunner::class)->run($set, $subject, $name);

            $url = match (true) {
                $result->redirect !== null => $result->redirect,
                $result->removed           => $set->indexUrl($subject) ?? url('/'),
                default                    => $this->pageUrl,
            };

            $this->redirect($url, navigate: true);
        });
    }

    public function render(): View
    {
        return view('boilerplate::livewire.actions.menu', ['items' => $this->items]);
    }

    private function actionSet(): ActionSet
    {
        abort_unless(is_subclass_of($this->set, ActionSet::class), 404);

        return app($this->set);
    }
}
