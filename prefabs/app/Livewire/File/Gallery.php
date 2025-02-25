<?php

namespace App\Livewire\File;

use App\Models\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use SplFileInfo;

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

    public function mount($model)
    {
        if (!empty($model)) {
            $this->model = $model;
            $this->refreshFiles();
        }
    }

    public function updatedFiles()
    {
        try {
            $validatedData = $this->validate();

            if (count($validatedData['files']) > 0) {
                foreach ($validatedData['files'] as $file) {
                    $this->model->uploadFile($file, "uploads" . DIRECTORY_SEPARATOR . class_basename(get_class($this->model)));
                }
                $this->refreshFiles();
                $this->dispatch('filesAdded');
            }
        } catch (\Throwable $th) {
            $this->refreshFiles();
            $this->addError('files', $th->getMessage());
        }
    }

    public function remove(File $file)
    {
        $file->delete();
        $this->refreshFiles();
    }

    public function render()
    {

        return view('livewire.file.gallery');
    }

    private function refreshFiles()
    {
        $this->files = [];
        foreach ($this->model->refresh()->files()->get() as $fileObj) {
            $file = new SplFileInfo($fileObj->path);
            $this->files[$fileObj->id] = route("file.serv", [
                "path"      => str_replace(DIRECTORY_SEPARATOR, '-', trim($file->getPath() .  DIRECTORY_SEPARATOR)),
                "file_name" => $file->getFilename(),
            ], false);
        }
    }
}
