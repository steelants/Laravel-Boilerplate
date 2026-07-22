<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use DOMDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Types\FileType;

class FileCollector
{
    protected string $prefix = '';

    public function setPrefix(string $prefix): static
    {
        $this->prefix = trim($prefix, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Default fragment cesty, pokud volající nezadá vlastní: $owner->filePath()
     * (granulární override na modelu), jinak {model}/{id}.
     */
    protected function defaultFragment(Model $owner): string
    {
        return method_exists($owner, 'filePath')
            ? trim($owner->filePath(), DIRECTORY_SEPARATOR)
            : Str::snake(class_basename($owner)) . DIRECTORY_SEPARATOR . $owner->getKey();
    }

    /**
     * Prefix se lepí vždy — ať je zbytek cesty default, nebo ho volající zadal explicitně.
     */
    protected function withPrefix(string $path): string
    {
        $segments = array_filter([$this->prefix, trim($path, DIRECTORY_SEPARATOR)], fn ($segment) => $segment !== null && $segment !== '');

        return implode(DIRECTORY_SEPARATOR, $segments);
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
            $imagesStoragePath = $this->defaultFragment($owner);
        }

        $imagesStoragePath = Str::lower($this->withPrefix($imagesStoragePath));

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

                Storage::drive($disk)->put(trim($imagesStoragePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename, file_get_contents($src));

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
            $rootPath = $this->defaultFragment($owner);
        }

        $rootPath = Str::lower($this->withPrefix($rootPath));

        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($rootPath, DIRECTORY_SEPARATOR), $file, $filename);

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

        return $this->loadFile($filename, $rootPath, $public);
    }

    public function loadFile(string $filename, string $rootPath, bool $public = false): string
    {
        return route('file.serv', [
            'path'      => str_replace(DIRECTORY_SEPARATOR, '-', trim($rootPath, DIRECTORY_SEPARATOR)),
            'file_name' => $filename,
            'public'    => $public,
        ], false);
    }

    /**
     * Disk se nikde nepersistuje — když ho volající nezná (např. Gallery se souborem, který nenahrával),
     * zjistí se podle toho, kde soubor reálně leží.
     */
    public function resolveDisk(string $rootPath, string $filename): string
    {
        $key = trim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        return Storage::disk('public')->exists($key) ? 'public' : 'local';
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

    public function uploadFileAnonymouse(UploadedFile|TemporaryUploadedFile $file, string $rootPath = '', bool $public = false): string
    {
        if (empty($rootPath)) {
            $rootPath = 'uploads';
        }

        $rootPath = $this->withPrefix($rootPath);

        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($rootPath, DIRECTORY_SEPARATOR), $file, $filename);

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

        return $this->loadFile($filename, $rootPath, $public);
    }

    public function replaceFile(File $fileModel, UploadedFile|TemporaryUploadedFile $file, bool $public = false): string
    {
        $disk = $public ? 'public' : 'local';
        Storage::drive($disk)->putFileAs(trim($fileModel->path, DIRECTORY_SEPARATOR), $file, $fileModel->filename);

        $fileModel->update([
            'original_name' => $file->getClientOriginalName(),
            'size'           => $file->getSize(),
        ]);

        return $this->loadFile($fileModel->filename, $fileModel->path, $public);
    }
}
