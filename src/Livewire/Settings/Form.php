<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Setting;

use App\Models\File;
use App\Models\Setting;
use App\Services\FileService;
use App\Types\SettingDataType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use SteelAnts\LivewireForm\Livewire\FormComponent;

class Form extends FormComponent
{
	use WithFileUploads;

	public $key;
	public $rules = [];

	public function mount($key)
    {
		$this->key = $key;

		foreach (Arr::get(config('setting_field'), $key) as $section) {
			foreach ($section as $key => $field) {
dd("adad");
			}
		}
	}

}
