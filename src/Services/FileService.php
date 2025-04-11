<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use DOMDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use  Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileService
{
	public static function parseInlineImages($rawContent, $imageFilePrefix = '', $imagesStoragePath = '', $imageLazyLoad = true)
    {
        if (empty($rawContent)) {
            return "";
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->loadHTML(mb_convert_encoding($rawContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimeType = $groups['mime'];

                $filename = $imageFilePrefix . uniqid('', true) . '.' . $mimeType;

                Storage::put($imagesStoragePath . $filename, file_get_contents($src));

                $image->removeAttribute('src');
                $image->setAttribute('src', route("file.serv", [
                    "path"      => str_replace('/', '-', trim($imagesStoragePath, '/')),
                    "file_name" => $filename,
                ], false));

                if ($imageLazyLoad) {
                    $image->setAttribute('loading', 'lazy');
                }
            }
        }

        return $dom->savehtml($dom->documentElement);
    }


	public static function uploadFile(Model $owner, UploadedFile|TemporaryUploadedFile $file, string $rootPath): string
    {
        $filename = Str::uuid()->toString() . "." . $file->getClientOriginalExtension();
        $file_path = $file->storeAs($rootPath, $filename);

        $owner->files()->updateOrCreate(
            [
                'filename' => $filename,
                'path'     => $file_path,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ],
        );

        return route("file.serv", [
            "path"      => str_replace(DIRECTORY_SEPARATOR, '-', trim($rootPath, DIRECTORY_SEPARATOR)),
            "file_name" => $filename,
        ], false);
    }
}