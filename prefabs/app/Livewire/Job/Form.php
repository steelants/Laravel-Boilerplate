<?php

namespace App\Livewire\Job;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use SteelAnts\LivewireForm\Livewire\FormComponent;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

class Form extends FormComponent
{
    public string $job;
    public array $job_parameters = [];
    public $note;
    public array $search = [];   // per-field search text
    public int $perPage = 50;

    public string $viewName = 'livewire.job.form';

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
                        'carbon' => $data['value'] ?? null,
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
        parent::mount();
        $this->job = $job;

		$class = '\\App\\Jobs\\' . $job;
        if (!class_exists($class)){
            $class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' . $job;
        }

        $reflection = new ReflectionClass($class);
        $this->note = $reflection->getDocComment();
        $params = $reflection->getConstructor()->getParameters();
        foreach ($params as $param) {
            $typeClass = $param->getType()?->getName();
            $reflBar = $reflection->getProperty($param->name);

            $this->job_parameters[$param->name] = [
                "type" => $typeClass
            ];
            if ($param->isDefaultValueAvailable()) {
                $this->job_parameters[$param->name]['value'] = $param->getDefaultValue();
            }
        }
    }

    public function submit(){
        Gate::authorize('is-admin');

        $class = '\\App\\Jobs\\' . $this->job;
        if (!class_exists($class)){
            $class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' .  $this->job;
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
                $this->properties[$name] = ($data['type'])::find($this->properties[$name]);
            }
        }

        dispatch(new $class(...$this->properties));

        $this->dispatch('snackbar', ['message' => ($this->job . ' ' . __('Job Scheduled')), 'type' => 'success', 'icon' => 'fas fa-check']);
        $this->dispatch('closeModal');
    }
}
