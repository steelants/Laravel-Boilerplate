<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use SteelAnts\LaravelBoilerplate\Types\SettingDataType;
use SteelAnts\LivewireForm\Livewire\FormComponent;

class Form extends FormComponent
{
	use WithFileUploads;

	public $key;
	public $rules = [];

	public function rules()
	{
		return $this->rules;
	}

	public function mount()
	{
		parent::mount();

		if (!empty(Arr::get(config('setting_field'), $this->key)) && count(Arr::get(config('setting_field'), $this->key)) > 0) {
			foreach (Arr::get(config('setting_field'), $this->key) as $key => $field) {
				$this->rules['properties.'.$key] = $field['rules'];
			}
		}
	}

	public function properties()
	{
		$properties = [];

		if (!empty(Arr::get(config('setting_field'), $this->key)) && count(Arr::get(config('setting_field'), $this->key)) > 0) {
			foreach (Arr::get(config('setting_field'), $this->key) as $key => $data) {
				if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
					$properties[$key] = match ($data['type']) {
						SettingDataType::INT  => $data['value'] ?? 0,
						SettingDataType::BOOL => $data['value'] ?? false,
						default => $data['value'] ?? "",
					};
				} else {
					$properties[$key] = $data['value'] ?? ($data['type'])::select('id')->first()->id;
				}
			}
		}

		return $properties;
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
				if (!class_exists($data['type']) || !is_subclass_of($data['type'], Model::class)) {
					$types[$key] = match ($data['type']) {
						SettingDataType::INT => 'int',
						SettingDataType::BOOL => 'checkbox',
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
