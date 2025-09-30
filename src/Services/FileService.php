<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use App\Models\File;
use App\Models\Tenant;
use DOMDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Models\File as ModelsFile;
use SteelAnts\LaravelBoilerplate\Types\FileType;

class FileService
{
    public static function parseInlineImages(Model $owner, $rawContent, $imageFilePrefix = '', $imagesStoragePath = '', $imageLazyLoad = true, Tenant $tenant = null)
    {
        if (empty($rawContent)) {
            return "";
        }

        if (empty($imageFilePrefix)) {
            $imageFilePrefix = Str::snake(class_basename($owner)) . '-';
        }

        if (empty($imagesStoragePath)) {
            $imagesStoragePath = 'uploads' . DIRECTORY_SEPARATOR . Str::snake($owner->getTable());
            if (method_exists($owner, 'rootPath')) {
                $imagesStoragePath = $owner->rootPath($imagesStoragePath);
            }
        }

		$imagesStoragePath = Str::lower($imagesStoragePath);

		if (!empty($tenant)) {
			static::addTenantPath($tenant, $imagesStoragePath);
		}

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->loadHTML(mb_convert_encoding($rawContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        $filesName = $owner->files()->where('type', FileType::INLINE)->pluck('filename', 'id')->toArray();

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            //unset file from files if exists
            if (!empty($filesName) && count($filesName) > 0 && !preg_match('/data:image/', $src)) {
                $nameParts = explode("\\", str_replace("/", "\\", urldecode($src)));
                unset($filesName[array_search(end($nameParts), $filesName)]);
            }

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimeType = $groups['mime'];

                $filename = $imageFilePrefix . uniqid('', true) . '.' . $mimeType;

                Storage::drive('local')->put(trim($imagesStoragePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($filename, DIRECTORY_SEPARATOR), file_get_contents($src));

                $owner->files()->updateOrCreate(
                    [
                        'filename' => $filename,
                        'path'     => $imagesStoragePath,
                    ],
                    [
                        'original_name' => $filename,
                        'size'          => strlen($src),
                        'type'          => FileType::INLINE,
                    ],
                );

                $image->removeAttribute('src');
                $image->setAttribute('src', static::loadFile($filename, $imagesStoragePath));

                if ($imageLazyLoad) {
                    $image->setAttribute('loading', 'lazy');
                }
            }
        }

        //remove all not unset files
        if (!empty($filesName) && count($filesName) > 0) {
            foreach ($filesName as $id => $path) {
                File::where('id', $id)->delete();
            }
        }

        #preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>',
        return $dom->savehtml($dom->documentElement);
    }

    public static function getInLineImagesFileIds(Model $owner, $rawContent): array
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->loadHTML(mb_convert_encoding($rawContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        $files = [];

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            $nameParts = explode("\\", str_replace("/", "\\", urldecode($src)));
            $file = $owner->files()->withoutGlobalScopes()->where('filename', end($nameParts))->first();
            if (!empty($file)) {
                $files[] = $file->id;
            }
        }

        return $files;
    }

    public static function uploadFile(Model $owner, UploadedFile|TemporaryUploadedFile $file, string $rootPath = "", bool $public = false, Tenant $tenant = null): string
    {
        $filename = Str::uuid()->toString() . "." . $file->getClientOriginalExtension();

		if (empty($rootPath)) {
            $rootPath = 'uploads' . DIRECTORY_SEPARATOR . Str::snake($owner->getTable());
            if (method_exists($owner, 'rootPath')) {
                $rootPath = $owner->rootPath($rootPath);
            }
        }

		$rootPath = Str::lower($rootPath);

		if (!empty($tenant)) {
			static::addTenantPath($tenant, $rootPath);
		}

		$drive = !empty($public) ? 'public' : 'local';
		Storage::drive($drive)->put(trim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename, $file);

        $owner->files()->updateOrCreate(
            [
                'filename' => $filename,
                'path'     => $rootPath,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ],
        );

        return static::loadFile($filename, $rootPath);
    }

    public static function loadFile(string $filename, string $rootPath, bool $public = false): string
    {
        return route("file.serv", [
            "path"      => str_replace(DIRECTORY_SEPARATOR, '-', trim($rootPath, DIRECTORY_SEPARATOR)),
            "file_name" => $filename,
			"public"    => $public,
        ], false);
    }

	public static function addTenantPath(Tenant $tenant, string &$rootPath): void
    {
		// /tenant_media/id tenant/your path
		$rootPath = DIRECTORY_SEPARATOR.'tenant_media'. DIRECTORY_SEPARATOR . $tenant->id . DIRECTORY_SEPARATOR . trim($rootPath, DIRECTORY_SEPARATOR);
    }

    public static function isImage($filename)
    {
        $imageExtensions = [
            'jpg',
            'jpeg',
            'gif',
            'png',
            'bmp',
            'svg',
            'svgz',
            'cgm',
            'djv',
            'djvu',
            'ico',
            'ief',
            'jpe',
            'pbm',
            'pgm',
            'pnm',
            'ppm',
            'ras',
            'rgb',
            'tif',
            'tiff',
            'wbmp',
            'xbm',
            'xpm',
            'xwd',
        ];
        $explode = explode(".", $filename);
        return in_array(end($explode), $imageExtensions);
    }

	public static function uploadFileAnonymouse(UploadedFile|TemporaryUploadedFile $file, string $rootPath, bool $public = false, Tenant $tenant = null): string
	{
		$filename = Str::uuid()->toString() . "." . $file->getClientOriginalExtension();
		if (!empty($tenant)) {
			static::addTenantPath($tenant, $rootPath);
		}
		$drive = !empty($public) ? 'public' : 'local';
		Storage::drive($drive)->put(trim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename, $file);

        File::updateOrCreate(
			[
				'filename' => $filename,
                'path'     => $rootPath,
            ],
            [
				'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ],
        );

        return "";
	}

	public static function replaceFile(ModelsFile $fileModel, UploadedFile|TemporaryUploadedFile $file): string
	{
		$rootPath = Str::rtrim(Str::remove($fileModel->filename, $fileModel->path), '/');
        $file_path = $file->storeAs($rootPath, $fileModel->filename);

        File::updateOrCreate(
			[
				'filename' => $fileModel->filename,
                'path'     => $file_path,
            ],
            [
				'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ],
        );

        return "";
	}
}
