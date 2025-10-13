<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Settings;

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

	public function mount()
    {
		foreach (Arr::get(config('setting_field'), $this->key) as $section) {
			foreach ($section as $key => $field) {
				dd("adad");
			}
		}
	}

}
