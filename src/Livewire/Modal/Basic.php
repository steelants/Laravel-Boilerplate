<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Modal;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;

class Basic extends Component
{
    protected $listeners = [
        'openModal',
        'closeModal',
    ];

    #[Locked]
    public $_livewireComponents = [];

    #[Locked]
    public $_livewireComponentParameters = [];

    public $model = null;

    public $parameters = [];

    public $modalId;

    public $title;

    public $size;

    public $static;

    /**
     * Open modal
     *
     * @param string|array $livewireComponents Component name, can be array
     * @param string $title Modal title
     * @param array $parameters Component parameters
     * @return void
     */
    public function openModal($livewireComponents, $title = "", $parameters = [], $size = 'md', $static = true)
    {
        foreach ((array) $livewireComponents as $component) {
            if (!$this->canOpenComponent($component)) {
                return;
            }
        }

        $this->_livewireComponents = $livewireComponents;
        $this->_livewireComponentParameters = $parameters;
        $this->title = $title;
        $this->size = $size;
        $this->static = $static;
    }

    public function closeModal()
    {
        $this->reset('_livewireComponents');
        $this->reset('_livewireComponentParameters');
        $this->reset('title');
    }

    public function mount()
    {
        $this->modalId = ('modal-' . Str::random(5));
    }

    public function render()
    {
        foreach ((array) $this->_livewireComponents as $component) {
            if (!$this->canOpenComponent($component)) {
                $this->dispatch('closeModal');

                return view('boilerplate::livewire.modal.basic', [
                    'livewireComponents'          => [],
                    'livewireComponentParameters' => [],
                ]);
            }
        }

        return view('boilerplate::livewire.modal.basic', [
            'livewireComponents'          => $this->_livewireComponents,
            'livewireComponentParameters' => $this->_livewireComponentParameters,
        ]);
    }

    private function canOpenComponent(string $alias): bool
    {
        $class = app(ComponentRegistry::class)->getClass($alias);

        if (!$class) {
            return false;
        }

        $attribute = $this->resolveAllowInModal($class);

        if (!$attribute) {
            return false;
        }

        if ($attribute->ability) {
            return Gate::allows($attribute->ability, $attribute->arguments);
        }

        return $attribute->asGuest || auth()->check();
    }

    private function resolveAllowInModal(string $class): ?AllowInModal
    {
        $attribute = (new ReflectionClass($class))->getAttributes(AllowInModal::class)[0] ?? null;

        if (!$attribute) {
            return null;
        }

        return new AllowInModal(...$this->normalizeArguments($attribute->getArguments()));
    }

    /**
     * Driv se v projektech psalo #[AllowInModal('is-system-admin')]. Retezec tim ale padal
     * do prvniho parametru bool $asGuest, PHP ho zkoalescovalo na true a kontrola opravneni
     * se nikdy neprovedla. Takovy zapis se proto prepise na pojmenovany argument ability.
     */
    private function normalizeArguments(array $arguments): array
    {
        if (isset($arguments[0]) && is_string($arguments[0]) && !isset($arguments['ability'])) {
            $arguments['ability'] = $arguments[0];
            unset($arguments[0]);
        }

        return $arguments;
    }
}
