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
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

#[AllowInModal('is-system-admin')]
class Form extends FormComponent
{
	use WithFileUploads;

	public $owner = null;

	public string $key;
	public array $rules = [];
	public array $settings_parameters = [];
	//TODO: Setting Field Load only once
	//TODO: add Help

	public function rules()
	{
		return $this->rules;
	}

	public function mount()
	{
		$this->settings_parameters = Arr::get(config('setting_field'), $this->key);

		if (!is_array($this->settings_parameters)) {
			throw new \Exception("Only array value can be specified in \$key");
		}

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $field) {
				if (!isset($field['type'])) {
					throw new \Exception("Only array value can be specified in \$key");
				}

				$this->rules['properties.'.$key] = $field['rules'];
			}
		}

		parent::mount();
	}

	public function properties()
	{
		$properties = [];

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $data) {
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
					$properties[$key] =  $this->owner->getSettings(implode('.', [$this->key, $key]), $properties[$key]);
				} else {
					$properties[$key] = settings(implode('.', [$this->key, $key]), $properties[$key]);
				}
			}
		}

		return $properties;
	}

	#[Computed()]
	public function fields()
	{
		$fields = [];

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $data) {
				$fields[] = $key;
			}
		}

		return $fields;
	}

	#[Computed()]
	public function types()
	{
		$types = [];

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $data) {
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

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $data) {
				$labels[$key] = Str::title($key);
			}
		}

		return $labels;
	}

	#[Computed()]
	public function helps()
	{
		$helps = [];

		if (!empty($this->settings_parameters) && count($this->settings_parameters) > 0) {
			foreach ($this->settings_parameters as $key => $data) {
				if (isset($data['help']) && !empty($data['help'])) {
					$helps[$key] = $data['help'];
				}
			}
		}

		return $helps;
	}

	public function getOptions($field, $options = []): array
	{
		$parameter = $this->settings_parameters[$field] ?? null;

		if ( empty($parameter) || !class_exists($parameter['type']) || !is_subclass_of($parameter['type'], \Illuminate\Database\Eloquent\Model::class) ) {
			return [];
		}

		return ($parameter['type'])::limit(1000)->pluck('name', 'id')->toArray();
	}

	protected function resolvePersistedType(string $key): string|int
	{
		if (isset($this->settings_parameters[$key]['type'])) {
			return $this->settings_parameters[$key]['type'];
		}

		return SettingDataType::STRING;
	}

	public function submit(){
		if (method_exists($this, 'rules')) {
			$this->validate();
		}

		foreach ($this->properties as $key => $value) {
			$dbKey = $this->key.'.'.$key;
			$type = $this->resolvePersistedType($key);
			if (!empty($this->owner)) {
				$this->owner->settings()->updateOrCreate(
					['index' => $dbKey],
					[
						'value' => $value ?? '',
						'type'  => $type,
					],
				);
			} else {
				Setting::updateOrCreate(
					['index' => $dbKey],
					[
						'value' => $value ?? '',
						'type'  => $type,
					],
				);
			}
		}

		$this->dispatch('snackbar', ['message' =>  __('Setting Saved'), 'type' => 'success', 'icon' => 'fas fa-check']);
		$this->dispatch('closeModal');
	}
}
