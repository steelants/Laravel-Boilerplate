<?php

use Illuminate\Support\Facades\DB;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\UserFixture;

describe('HasSettings::getSettings()', function () {
    it('returns the stored per-model value', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        $user->settings()->create(['index' => 'theme', 'value' => 'dark', 'type' => 'string']);

        expect($user->getSettings('theme'))->toBe('dark');
    });

    it('returns explicit default when row missing', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        expect($user->getSettings('missing', 'light'))->toBe('light');
    });

    it('falls back to config when default is null', function () {
        $user = UserFixture::create(['name' => 'Joe']);

        expect($user->getSettings('test.from.config'))->toBe('config-default-value');
    });

    it('does not leak settings between different owners', function () {
        $a = UserFixture::create(['name' => 'A']);
        $b = UserFixture::create(['name' => 'B']);
        $a->settings()->create(['index' => 'theme', 'value' => 'dark', 'type' => 'string']);

        expect($a->getSettings('theme'))->toBe('dark')
            ->and($b->getSettings('theme', 'light'))->toBe('light');
    });

    it('does not collide with global settings of the same key', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        Setting::create(['index' => 'theme', 'value' => 'global-dark', 'type' => 'string']);

        expect($user->getSettings('theme', 'fallback'))->toBe('fallback')
            ->and(settings('theme'))->toBe('global-dark');
    });

    it('preserves "0" as value', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        $user->settings()->create(['index' => 'feature.flag', 'value' => '0', 'type' => 'int']);

        expect($user->getSettings('feature.flag', 'should-not-apply'))->toBe('0');
    });

    it('uses eager-loaded relation — multiple calls hit DB only once', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        $user->settings()->create(['index' => 'a', 'value' => 'A', 'type' => 'string']);
        $user->settings()->create(['index' => 'b', 'value' => 'B', 'type' => 'string']);

        $fresh = UserFixture::with('settings')->find($user->id);

        DB::enableQueryLog();
        DB::flushQueryLog();

        $fresh->getSettings('a');
        $fresh->getSettings('b');
        $fresh->getSettings('missing');

        expect(DB::getQueryLog())->toHaveCount(0);
    });
});
