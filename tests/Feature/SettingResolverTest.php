<?php

use SteelAnts\LaravelBoilerplate\Support\SettingResolver;

describe('SettingResolver::resolve()', function () {
    it('returns explicit default for null matches', function () {
        expect(SettingResolver::resolve(null, 'any.key', 'fallback'))->toBe('fallback');
    });

    it('returns explicit default for empty collection', function () {
        expect(SettingResolver::resolve(collect(), 'any.key', 'fallback'))->toBe('fallback');
    });

    it('returns config value when default is null and config has it', function () {
        expect(SettingResolver::resolve(null, 'test.from.config', null))->toBe('config-default-value');
    });

    it('returns the single value when one match', function () {
        $matches = collect([(object) ['index' => 'k', 'value' => 'V']]);

        expect(SettingResolver::resolve($matches, 'k', 'fallback'))->toBe('V');
    });

    it('returns pluck array when multiple matches', function () {
        $matches = collect([
            (object) ['index' => 'a', 'value' => 'A'],
            (object) ['index' => 'b', 'value' => 'B'],
        ]);

        expect(SettingResolver::resolve($matches, 'shared', null))
            ->toBe(['a' => 'A', 'b' => 'B']);
    });
});
