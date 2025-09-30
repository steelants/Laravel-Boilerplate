<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\File;

use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use SplFileInfo;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Services\FileService;
use Throwable;

class Gallery extends Component
{
    use WithFileUploads;

    public $model;
    public $files = [];
	public $files_replacements = [];

    public $uploadEnabled = true;
    public $replaceEnabled = true;

    public $listeners = ['filesAdded' => '$refresh'];

    protected function rules()
    {
        return [
			'files'                  => 'required|array',
			'files.*'                => 'required|image',
			'files_replacements'     => 'required|array',
			'files_replacements.*'   => 'required|exists:files,id',
			'files_replacements.*.*' => 'required|image',
		];
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
		if (!$this->uploadEnabled) {
			return;
		}

		try {
			$validatedData = $this->validateOnly('files');
			if (count($validatedData['files']) > 0) {
				foreach ($validatedData['files'] as $file) {
					if (!empty($this->model)) {
						$this->model->uploadFile($file);
					} else {
						FileService::uploadFileAnonymouse($file, "uploads");
					}
				}

				$this->refreshFiles();
				$this->dispatch('filesAdded');
				$this->dispatch('snackbar', ['message' => "test", 'type' => 'sucess', 'icon' => 'fas fa-check']);
			}
        } catch (Throwable $th) {
            $this->refreshFiles();
            $this->addError('files', $th->getMessage());
			$this->dispatch('snackbar', ['message' => $th->getMessage(), 'type' => 'error', 'icon' => 'fas fa-x']);
        }
    }

	public function updatedFilesReplacements(TemporaryUploadedFile $file, File $fileModel)
    {
		if (!$this->replaceEnabled) {
			return;
		}

		try {
			$validatedData = $this->validateOnly('files_replacements');
			if (!empty($this->model)) {
				$this->model->replaceFile($fileModel, $file);
			} else {
				FileService::replaceFile($fileModel, $file);
			}

			$this->refreshFiles();
			$this->dispatch('filesAdded');
			$this->dispatch('snackbar', ['message' => "test", 'type' => 'sucess', 'icon' => 'fas fa-check']);
		} catch (Throwable $th) {
            $this->refreshFiles();
            $this->addError('files_replacements.' . $fileModel->id, $th->getMessage());
			$this->dispatch('snackbar', ['message' => $th->getMessage(), 'type' => 'error', 'icon' => 'fas fa-x']);
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
            $file = new SplFileInfo($fileObj->path . DIRECTORY_SEPARATOR . $fileObj->filename);
            $this->files[$fileObj->id] = FileService::loadFile($file->getFilename(), $file->getPath()). '?t=' . $fileObj->updated_at;
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
