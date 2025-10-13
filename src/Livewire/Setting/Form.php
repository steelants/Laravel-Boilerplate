<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Types\SettingDataType;
use SteelAnts\LivewireForm\Livewire\FormComponent;

class Form extends FormComponent
{
	use WithFileUploads;

	public $owner;
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

				if (!empty($this->owner)) {
					$properties[$key] =  $this->owner->settings()->where('index', implode('.',[$this->key, $key]))->first()->value;
				} else {
					$properties[$key] = settings(implode('.',[$this->key, $key]), $properties[$key]);
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

		if ( empty($parameter) || !class_exists($parameter['type']) || !is_subclass_of($parameter['type'], \Illuminate\Database\Eloquent\Model::class) ) {
			return [];
		}

		return ($parameter['type'])::limit(1000)->pluck('name', 'id')->toArray();
	}

	public function submit(){
		if (method_exists($this, 'rules')) {
			$this->validate();
		}

		foreach ($this->properties as $key => $value) {
			$dbKey = $this->key.'.'.$key;
			if (!empty($this->owner)) {
				$this->owner->settings()->updateOrCreate(
					['index' => $dbKey],
					[
						'value' => $value ?? '',
						'type'  => $this->types[$key] ?? Arr::get(config('setting_field'), $this->key)[$key]['type'],
					],
				);
			} else {
				Setting::updateOrCreate(
					['index' => $dbKey],
					[
						'value' => $value ?? '',
						'type'  => $this->types[$key] ?? Arr::get(config('setting_field'), $this->key)[$key]['type'],
					],
				);
			}
		}



		$this->dispatch('snackbar', ['message' =>  __('Setting Saved'), 'type' => 'success', 'icon' => 'fas fa-check']);
		$this->dispatch('closeModal');
	}

	function onSuccess(){
		//DO SOMETHING ON SUCESS;
	}

	function onError(){
		//DO SOMETHING ON ERROR;
	}
}
