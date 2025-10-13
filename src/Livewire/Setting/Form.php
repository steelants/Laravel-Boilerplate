<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Setting;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use SteelAnts\LaravelBoilerplate\Types\SettingDataType;
use SteelAnts\LivewireForm\Livewire\FormComponent;
use Illuminate\Support\Str;

class Form extends FormComponent
{
	use WithFileUploads;

	public $key;
	public $rules = [];
	public $options = [];

	public function rules()
	{
		return $this->rules;
	}

	public function mount()
	{
		foreach (Arr::get(config('setting_field'), $this->key) as $key => $field) {
			$this->rules['values.'.$key] = $field['rules'];

			switch ($field['type']) {
				case (SettingDataType::MODEL):
					$modelClass = 'App\\Models\\'.ucfirst($field['model']);
					$this->options[$key] = $modelClass::where('organization_id', tenant()->id)->pluck('name', 'id')->toArray();
					break;

				case (SettingDataType::SELECT):
					$this->options[$key] = $field['values'];
					break;

				case (SettingDataType::FILE):
					$this->isPublicFiles[$key] = $field['public'] ?? false;
					break;

				default:
					break;
			}

		}
	}

	#[Computed()]
    public function fields()
    {
        $fields = [];

        if (!empty(Arr::get(config('setting_field'), $this->key)) && count(Arr::get(config('setting_field'), $this->key)) > 0) {
            foreach (Arr::get(config('setting_field'), $this->key) as $key => $data) {
                $fields[] = $key;
            }
        }

        return $fields;
    }

	#[Computed()]
    public function types()
    {
        $types = [];

		if (!empty(Arr::get(config('setting_field'), $this->key)) && count(Arr::get(config('setting_field'), $this->key)) > 0) {
            foreach (Arr::get(config('setting_field'), $this->key) as $key => $data) {
                $types[$key] = match($data['type']) {
					SettingDataType::INT => 'int',
					SettingDataType::BOOL => 'checkbox',
					default => 'string',
				};
            }
        }

        return $types;
    }

	#[Computed()]
    public function labels()
    {
        $labels = [];

       if (!empty(Arr::get(config('setting_field'), $this->key)) && count(Arr::get(config('setting_field'), $this->key)) > 0) {
            foreach (Arr::get(config('setting_field'), $this->key) as $key => $data) {
                $labels[$key] = Str::title($key);
            }
        }

        return $labels;
    }

	public function getOptions($field, $options = []): array
    {
        $parameter = Arr::get(config('setting_field'), $this->key)[$field] ?? null;
dd($parameter);
        if (
            empty($parameter)
            || !class_exists($parameter['type'])
            || !is_subclass_of($parameter['type'], \Illuminate\Database\Eloquent\Model::class)
        ) {
            return [];
        }

        return ($parameter['type'])::limit(1000)->pluck('name', 'id')->toArray();
    }

}
