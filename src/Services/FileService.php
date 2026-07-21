<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use DOMDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Types\FileType;

class FileService
{
    protected string $prefix = '';

    public function setPrefix(string $prefix): static
    {
        $this->prefix = trim($prefix, '/');

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Jediný zdroj pravdy pro stavbu cesty: {prefix}/{model}/{id}, výhradně přes '/'.
     */
    protected function buildDirectory(Model $owner): string
    {
        $model = Str::snake(class_basename($owner));
        $segments = array_filter([$this->prefix, $model, $owner->getKey()], fn ($segment) => $segment !== null && $segment !== '');

        return implode('/', $segments);
    }

    public function parseInlineImages(Model $owner, $rawContent, $imageFilePrefix = '', $imagesStoragePath = '', $imageLazyLoad = true, bool $public = false)
    {
        if (empty($rawContent)) {
            return '';
        }

        if (empty($imageFilePrefix)) {
            $imageFilePrefix = Str::snake(class_basename($owner)) . '-';
        }

        if (empty($imagesStoragePath)) {
            $imagesStoragePath = $this->buildDirectory($owner);
        }

        $imagesStoragePath = Str::lower($imagesStoragePath);

        libxml_use_internal_errors(true);

        $dom = new DOMDocument;
        $dom->encoding = 'utf-8';
        $dom->loadHTML(mb_convert_encoding($rawContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        $filesName = $owner->files()->where('type', FileType::INLINE)->pluck('filename', 'id')->toArray();

        $disk = $public ? 'public' : 'local';

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            // unset file from files if exists
            if (!empty($filesName) && count($filesName) > 0 && !preg_match('/data:image/', $src)) {
                $nameParts = explode('/', urldecode($src));
                unset($filesName[array_search(end($nameParts), $filesName)]);
            }

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimeType = $groups['mime'];

                $filename = $imageFilePrefix . uniqid('', true) . '.' . $mimeType;

                Storage::drive($disk)->put(trim($imagesStoragePath, '/') . '/' . $filename, file_get_contents($src));

                $owner->files()->updateOrCreate(
                    [
                        'filename' => $filename,
                        'path'     => $imagesStoragePath,
                    ],
                    [
                        'original_name' => $filename,
                        'size'          => strlen($src),
                        'type'          => FileType::INLINE,
                        'disk'          => $disk,
                    ],
                );

                $image->removeAttribute('src');
                $image->setAttribute('src', $this->loadFile($filename, $imagesStoragePath, $public));

                if ($imageLazyLoad) {
                    $image->setAttribute('loading', 'lazy');
                }
            }
        }

        // remove all not unset files
        if (!empty($filesName) && count($filesName) > 0) {
            foreach ($filesName as $id => $path) {
                File::where('id', $id)->delete();
            }
        }

        // preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>',
        return $dom->savehtml($dom->documentElement);
    }

    public function getInLineImagesFileIds(Model $owner, $rawContent): array
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument;
        $dom->encoding = 'utf-8';
        $dom->loadHTML(mb_convert_encoding($rawContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        $files = [];

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            $nameParts = explode('/', urldecode($src));
            $file = $owner->files()->withoutGlobalScopes()->where('filename', end($nameParts))->first();
            if (!empty($file)) {
                $files[] = $file->id;
            }
        }

        return $files;
    }

    public function uploadFile(Model $owner, UploadedFile|TemporaryUploadedFile $file, string $rootPath = '', bool $public = false): string
    {
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

        if (empty($rootPath)) {
            $rootPath = $this->buildDirectory($owner);
        }

        $rootPath = Str::lower($rootPath);

        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($rootPath, '/'), $file, $filename);

        $owner->files()->updateOrCreate(
            [
                'filename' => $filename,
                'path'     => $rootPath,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
                'disk'          => $disk,
            ],
        );

        return $this->loadFile($filename, $rootPath, $public);
    }

    public function loadFile(string $filename, string $rootPath, bool $public = false): string
    {
        return route('file.serv', [
            'path'      => str_replace('/', '-', trim($rootPath, '/')),
            'file_name' => $filename,
            'public'    => $public,
        ], false);
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
        $explode = explode('.', $filename);

        return in_array(end($explode), $imageExtensions);
    }

    public function uploadFileAnonymouse(UploadedFile|TemporaryUploadedFile $file, string $rootPath, bool $public = false): string
    {
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($rootPath, '/'), $file, $filename);

        File::updateOrCreate(
            [
                'filename' => $filename,
                'path'     => $rootPath,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
                'disk'          => $disk,
            ],
        );

        return $this->loadFile($filename, $rootPath, $public);
    }

    public function replaceFile(File $fileModel, UploadedFile|TemporaryUploadedFile $file, bool $public = false): string
    {
        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($fileModel->path, '/'), $file, $fileModel->filename);

        $fileModel->update([
            'original_name' => $file->getClientOriginalName(),
            'size'           => $file->getSize(),
            'disk'           => $disk,
        ]);

        return $this->loadFile($fileModel->filename, $fileModel->path, $public);
    }

    /**
     * @deprecated Statické volání je zpětně kompatibilní wrapper. Použij FileStorage facade nebo app(FileService::class).
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return app(static::class)->$method(...$arguments);
    }
}
