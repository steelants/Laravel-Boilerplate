<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Setting;

use Illuminate\Support\Arr;
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
		foreach (Arr::get(config('setting_field'), $this->key) as $key => $field) {
				$this->rules['values.'.$key] = $field['rules'];

				switch ($field['type']) {
					case (SettingDataType::MODEL):
						break;

					case (SettingDataType::SELECT):
						break;

					default:
						break;
				}

		}
	}

}
