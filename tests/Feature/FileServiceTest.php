<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SteelAnts\LaravelBoilerplate\Facades\FileStorage;
use SteelAnts\LaravelBoilerplate\Services\FileService;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\UserFixture;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
    app(FileService::class)->setPrefix('');
});

describe('FileService::buildDirectory() (via uploadFile)', function () {
    it('builds {model}/{id} without a prefix', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        $user->uploadFile(UploadedFile::fake()->image('avatar.png'));

        expect($user->files()->first()->path)->toBe('user_fixture/' . $user->id);
    });

    it('builds {prefix}/{model}/{id} once a prefix is set', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        app(FileService::class)->setPrefix('tenant_media/5');

        $user->uploadFile(UploadedFile::fake()->image('avatar.png'));

        expect($user->files()->first()->path)->toBe('tenant_media/5/user_fixture/' . $user->id);
    });

    it('never uses backslashes, only "/"', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        app(FileService::class)->setPrefix('tenant_media/5');

        $user->uploadFile(UploadedFile::fake()->image('avatar.png'));

        expect($user->files()->first()->path)->not->toContain('\\');
    });
});

describe('FileService::uploadFile()', function () {
    it('stores the file on the public disk and persists disk=public', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        $user->uploadFile(UploadedFile::fake()->image('avatar.png'), public: true);
        $file = $user->files()->first();

        expect($file->disk)->toBe('public');
        Storage::disk('public')->assertExists($file->path . '/' . $file->filename);
        Storage::disk('local')->assertMissing($file->path . '/' . $file->filename);
    });

    it('stores the file on the local disk and persists disk=local', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        $user->uploadFile(UploadedFile::fake()->image('avatar.png'));
        $file = $user->files()->first();

        expect($file->disk)->toBe('local');
        Storage::disk('local')->assertExists($file->path . '/' . $file->filename);
    });

    it('returns a link matching the disk the file was stored on', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        $link = $user->uploadFile(UploadedFile::fake()->image('avatar.png'), public: true);

        expect($link)->toContain('public=1');
    });
});

describe('FileObserver::deleting()', function () {
    it('deletes the file from the disk it was actually stored on', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        $user->uploadFile(UploadedFile::fake()->image('avatar.png'), public: true);
        $file = $user->files()->first();
        $key = $file->path . '/' . $file->filename;

        Storage::disk('public')->assertExists($key);

        $file->delete();

        Storage::disk('public')->assertMissing($key);
    });
});

describe('Fileable::replaceFile()', function () {
    it('replaces the content of the given File model, not a collection', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        $user->uploadFile(UploadedFile::fake()->image('avatar.png'));
        $file = $user->files()->first();
        $originalFilename = $file->filename;

        $user->replaceFile($file, UploadedFile::fake()->image('new.png', 20, 20));
        $file->refresh();

        expect($file->filename)->toBe($originalFilename);
        Storage::disk('local')->assertExists($file->path . '/' . $file->filename);
    });
});

describe('FileService static back-compat', function () {
    it('forwards deprecated static calls to the container-bound instance', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        FileService::uploadFile($user, UploadedFile::fake()->image('avatar.png'));

        expect($user->files()->count())->toBe(1);
    });
});

it('exposes the same singleton through the FileStorage facade', function () {
    expect(FileStorage::getFacadeRoot())->toBe(app(FileService::class));
});
