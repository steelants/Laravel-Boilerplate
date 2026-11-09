<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Modal;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;

class Basic extends Component
{
    private const SIZES = ['sm', 'md', 'lg', 'xl'];

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

    /*
     * Zamerne bez PHP typu: Livewire TypeError pri updatu meni na abort(419) bez reportu chyby.
     * Neplatne hodnoty proto vyhazuji InvalidArgumentException, ktera se reportuje normalne.
     *
     * title neni Locked: event set-modal-title ho meni jen v prohlizeci ($wire.title, bez requestu
     * navic) a na server dorazi s dalsim requestem. Jde o zobrazovany text, Blade ho escapuje,
     * a updatedTitle() kontroluje typ.
     */

    /** @var string */
    public $title;

    /** @var string */
    #[Locked]
    public $size;

    /** @var bool */
    #[Locked]
    public $static;

    /**
     * Open modal
     *
     * Parametry nemaji PHP typy schvalne - TypeError by Livewire v produkci prevedl na 419 Page
     * expired a chyba by se nereportovala. Kontrola je proto rucni a vyhazuje InvalidArgumentException.
     *
     * @param string|array $livewireComponents Component name, can be array
     * @param string|null $title Modal title
     * @param array $parameters Component parameters
     * @param 'sm'|'md'|'lg'|'xl' $size Modal size
     * @param bool $static Static backdrop, modal does not close on click outside or Esc
     * @return void
     * @throws InvalidArgumentException
     */
    public function openModal($livewireComponents, $title = "", $parameters = [], $size = 'md', $static = true)
    {
        if ($title !== null && !is_string($title)) {
            throw new InvalidArgumentException(sprintf('Modal title must be a string, %s given (components: %s).', get_debug_type($title), json_encode($livewireComponents)));
        }

        if (!in_array($size, self::SIZES, true)) {
            throw new InvalidArgumentException(sprintf('Modal size must be one of %s, %s given (components: %s).', implode(', ', self::SIZES), is_string($size) ? "'{$size}'" : get_debug_type($size), json_encode($livewireComponents)));
        }

        if (!is_bool($static)) {
            throw new InvalidArgumentException(sprintf('Modal static must be a bool, %s given (components: %s).', get_debug_type($static), json_encode($livewireComponents)));
        }

        foreach ((array) $livewireComponents as $component) {
            if (!$this->canOpenComponent($component)) {
                return;
            }
        }

        $this->_livewireComponents = $livewireComponents;
        $this->_livewireComponentParameters = $parameters;
        $this->title = $title ?? '';
        $this->size = $size;
        $this->static = $static;
    }

    public function updatedTitle()
    {
        if ($this->title !== null && !is_string($this->title)) {
            throw new InvalidArgumentException(sprintf('Modal title must be a string, %s given (components: %s).', get_debug_type($this->title), json_encode($this->_livewireComponents)));
        }
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
        try {
            // Livewire 3 has the ComponentRegistry, Livewire 4 resolves components through its factory
            $class = class_exists(ComponentRegistry::class)
                ? app(ComponentRegistry::class)->getClass($alias)
                : app('livewire.factory')->resolveComponentClass($alias);
        } catch (ComponentNotFoundException) {
            return false;
        }

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
