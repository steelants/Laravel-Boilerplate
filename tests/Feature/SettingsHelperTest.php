<?php

use Illuminate\Support\Facades\DB;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\UserFixture;

describe('settings() helper', function () {
    it('returns the stored value when row exists', function () {
        Setting::create(['index' => 'app.name', 'value' => 'My App', 'type' => 'string']);

        expect(settings('app.name'))->toBe('My App');
    });

    it('returns explicit default when row missing', function () {
        expect(settings('missing.key', 'fallback'))->toBe('fallback');
    });

    it('returns null when no default and no config value', function () {
        expect(settings('totally.missing'))->toBeNull();
    });

    it('falls back to config when default is null and config has value', function () {
        expect(settings('test.from.config'))->toBe('config-default-value');
    });

    it('explicit default wins over config fallback', function () {
        expect(settings('test.from.config', 'explicit'))->toBe('explicit');
    });

    it('preserves "0" as value (=== null semantics)', function () {
        Setting::create(['index' => 'feature.enabled', 'value' => '0', 'type' => 'int']);

        expect(settings('feature.enabled', 'default-should-not-apply'))->toBe('0');
    });

    it('preserves empty string as value', function () {
        Setting::create(['index' => 'app.suffix', 'value' => '', 'type' => 'string']);

        expect(settings('app.suffix', 'default-should-not-apply'))->toBe('');
    });

    it('memoizes per request — multiple calls hit DB only once', function () {
        Setting::create(['index' => 'a', 'value' => 'A', 'type' => 'string']);
        Setting::create(['index' => 'b', 'value' => 'B', 'type' => 'string']);

        DB::enableQueryLog();
        DB::flushQueryLog();

        settings('a');
        settings('b');
        settings('a');
        settings('missing');

        expect(DB::getQueryLog())->toHaveCount(1);
    });

    it('ignores rows scoped to a specific settable owner', function () {
        $user = UserFixture::create(['name' => 'Joe']);
        Setting::create([
            'settable_type' => $user::class,
            'settable_id'   => $user->id,
            'index'         => 'profile.theme',
            'value'         => 'dark',
            'type'          => 'string',
        ]);

        expect(settings('profile.theme'))->toBeNull();
    });
});
