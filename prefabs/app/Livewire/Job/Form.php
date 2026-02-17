<?php

namespace App\Livewire\Job;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use SteelAnts\LivewireForm\Livewire\FormComponent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

#[AllowInModal('is-system-admin')]
class Form extends FormComponent
{
    public string $job;
    public array $job_parameters = [];
    public $note;
    public array $search = [];   // per-field search text
    public int $perPage = 50;

    public string $viewName = 'livewire.job.form';

    protected function rules()
    {
        $rules = [];
        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                $rules['properties.' . $name] = [];
                $rules['properties.' . $name][] = !isset($data['value']) ? 'required' : 'nullable';
                if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
                    $rules['properties.' . $name][] = match($data['type']) {
                        'int', 'float' => 'numeric',
                        'bool' => 'boolean',
                        Carbon::class => 'date',
                        SupportCarbon::class=> 'date',
                        default => 'string',
                    };
                } else {
                    $rules['properties.' . $name][] = Rule::exists(($data['type'])::first()->getTable(), 'id');
                }
            }
        }
        return $rules;
    }

    #[Computed()]
    public function fields()
    {
        $fields = [];

        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                $fields[] = $name;
            }
        }

        return $fields;
    }

    public function properties()
    {
        $properties = [];

        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
                    $properties[$name] = match($data['type']) {
                        'int', 'float' => $data['value'] ?? 0,
                        'bool' => $data['value'] ?? false,
                        Carbon::class => $data['value'] ?? null,
                        SupportCarbon::class => $data['value'] ?? null,
                        default => $data['value'] ?? "",
                    };
                } else {
                    $properties[$name] = $data['value'] ?? ($data['type'])::select('id')->first()->id;
                }
            }
        }

        return $properties;
    }

    #[Computed()]
    public function types()
    {
        $types = [];
        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
                    $types[$name] = match($data['type']) {
                        'int', 'float' => 'int',
                        'bool' => 'checkbox',
                        Carbon::class => 'datetime',
                        SupportCarbon::class => 'datetime',
                        default => 'string',
                    };
                }
            }
        }
        return $types;
    }

    #[Computed()]
    public function labels()
    {
        $labels = [];

        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                $labels[$name] = Str::title($name);
            }
        }

        return $labels;
    }

    public function getOptions($field, $options = []): array
    {
        $parameter = $this->job_parameters[$field] ?? null;

        if (
            empty($parameter)
            || !class_exists($parameter['type'])
            || !is_subclass_of($parameter['type'], \Illuminate\Database\Eloquent\Model::class)
        ) {
            return [];
        }

        return ($parameter['type'])::limit(1000)->pluck('name', 'id')->toArray();
    }

    public function mount($job = null)
    {
		$this->job = $job;

		$class = '\\App\\Jobs\\' . $job;
        if (!class_exists($class)){
			$class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' . $job;
			if (!class_exists($class)){
				$class = '\\SteelAnts\\LaravelBoilerplate\\Dashboard\\Jobs\\' .  $this->job;
			}
        }

        $reflection = new ReflectionClass($class);
        $this->note = $reflection->getDocComment();
        $params = $reflection->getConstructor()->getParameters();
        foreach ($params as $param) {
            $type = $param->getType();
            if ($type instanceof \ReflectionUnionType) {
                $nonString = array_filter($type->getTypes(), fn($t) => $t->getName() !== 'string');
                $typeClass = !empty($nonString) ? reset($nonString)->getName() : $type->getTypes()[0]->getName();
            } else {
                $typeClass = $type?->getName();
            }

            $this->job_parameters[$param->name] = [
				"type" => $typeClass
            ];

            if ($param->isDefaultValueAvailable()) {
				$this->job_parameters[$param->name]['value'] = $param->getDefaultValue();
            }
        }

		parent::mount();
    }

    public function submit(){
        Gate::authorize('is-system-admin');

        if (method_exists($this, 'rules')) {
            $this->validate();
        }

        $class = '\\App\\Jobs\\' . $this->job;
        if (!class_exists($class)){
            $class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' .  $this->job;
			if (!class_exists($class)){
				$class = '\\SteelAnts\\LaravelBoilerplate\\Dashboard\\Jobs\\' .  $this->job;
			}
        }

        if (!empty($this->job_parameters) && count($this->job_parameters) > 0) {
            foreach ($this->job_parameters as $name => $data) {
                if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
                    $this->properties[$name] = match($data['type']) {
                        'int' => (int)($this->properties[$name] ?? 0),
                        'float' => (float)($this->properties[$name] ?? 0.0),
                        'bool' => (bool)($this->properties[$name] ?? false),
                        Carbon::class => !empty($this->properties[$name]) ? Carbon::parse($this->properties[$name]) : null,
                        SupportCarbon::class => !empty($this->properties[$name]) ? SupportCarbon::parse($this->properties[$name]) : null,
                        default => (string)($this->properties[$name] ?? ""),
                    };
                    continue;
                }
                $this->properties[$name] = !empty($this->properties[$name]) ? ($data['type'])::find($this->properties[$name]) : null;
            }
        }

        dispatch(new $class(...$this->properties));

        $this->dispatch('snackbar', ['message' => ($this->job . ' ' . __('Job Scheduled')), 'type' => 'success', 'icon' => 'fas fa-check']);
        $this->dispatch('closeModal');
    }

    function onSuccess(){
        //DO SOMETHING ON SUCESS;
    }

    function onError(){
        //DO SOMETHING ON ERROR;
    }
}
