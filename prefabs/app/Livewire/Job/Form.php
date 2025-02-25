<?php

namespace App\Livewire\Job;

use Livewire\Component;
use App\Types\PermissionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;

class Form extends Component
{
    public string $job;
    public array $job_parameters = [];
    public array $job_parameters_value = [];
    public $note;

    protected function rules()
    {
        return [
            'rule_class'     => 'required',
            'rules_values.*' => 'required',
        ];
    }

    public function mount($job)
    {
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
                $typeClass,
                "",
            ];

            // $value = $reflBar->getValue($reflection->newInstanceWithoutConstructor());
            // if (!in_array($typeClass, ["string", "int", "bool"])) {
            //     $this->job_parameters_value[$param->name] = is_object($value) ? $value->id : null;
            // } else {
            //     $this->job_parameters_value[$param->name] = $value;
            // }
        }
    }

    public function run(Request $request, $job)
    {
		Gate::authorize('is-admin');

        $class = '\\App\\Jobs\\' . $job;
        if (!class_exists($class)){
            $class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' . $job;
        }

        dispatch(new $class(...$this->job_parameters_value));

        $this->dispatch('snackbar', ['message' => ($job . ' ' . __('Job Scheduled')), 'type' => 'success', 'icon' => 'fas fa-check']);
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.job.form');
    }
}
