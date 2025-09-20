<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\File;

use SteelAnts\LaravelBoilerplate\Models\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use SplFileInfo;
use SteelAnts\LaravelBoilerplate\Services\FileService;
use Throwable;

class Gallery extends Component
{
    use WithFileUploads;

    public $model;
    public $files = [];
    public $uploadEnabled = true;

    public $listeners = ['filesAdded' => '$refresh'];

    protected function rules()
    {
        return ['files.*' => 'required|image'];
    }

    public function mount($model = null)
    {
        if (!empty($model)) {
            $this->model = $model;
        }
		$this->refreshFiles();
    }

    public function updatedFiles()
    {
        try {
            $validatedData = $this->validate();

            if (count($validatedData['files']) > 0) {
                foreach ($validatedData['files'] as $file) {
					if (!empty($this->model)) {
						$this->model->uploadFile($file, "uploads" . DIRECTORY_SEPARATOR . class_basename(get_class($this->model)));
					} else {
						FileService::uploadFileAnonymouse($file, "uploads");
					}
                }

                $this->refreshFiles();
                $this->dispatch('filesAdded');
            }
        } catch (Throwable $th) {
            $this->refreshFiles();
            $this->addError('files', $th->getMessage());
        }
    }

    public function remove(File $file)
    {
        $file->delete();
        $this->refreshFiles();
    }

	public function replace(File $file)
    {
        $file->delete();
        $this->refreshFiles();
    }

    public function render()
    {
        return view('boilerplate::livewire.file.gallery');
    }

    private function refreshFiles()
    {
		if (!empty($this->model)) {
			$files = $this->model->refresh()->files()->get();
        } else {
			$files = File::all();
		}

        $this->files = [];
        foreach ($files as $fileObj) {
            $file = new SplFileInfo($fileObj->path);
            $this->files[$fileObj->id] = route("file.serv", [
                "path"      => str_replace(DIRECTORY_SEPARATOR, '-', trim($file->getPath() .  DIRECTORY_SEPARATOR)),
                "file_name" => $file->getFilename(),
            ], false);
        }
    }

	public function placeholder()
    {
        return <<<'HTML'
        <div class="text-center py-10">
            <div class="spinner-border text-secondary" role="status"></div>
        </div>
        HTML;
    }
}
