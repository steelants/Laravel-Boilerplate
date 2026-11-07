<?php

use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use SteelAnts\LaravelBoilerplate\Livewire\Actions\Menu;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryActions;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryFixture;

require_once __DIR__ . '/../../Fixtures/ActionsFixture.php';

// Menu akcí v detailu (<livewire:boilerplate.actions.menu :subject="...">) - sadu najde přes #[UseActions] na třídě objektu.

beforeEach(function () {
    LogEntryFixture::$store = [];
    Gate::define('resolve-log', fn ($user = null) => true);
});

test('the menu is registered under the boilerplate prefix and lists the actions of the subject', function () {
    $component = Livewire::test('boilerplate.actions.menu', ['subject' => new LogEntryFixture('a1')]);

    expect($component->get('set'))->toBe(LogEntryActions::class)
        ->and(array_map(fn ($item) => $item->name, $component->instance()->items()))->toBe(['show', 'resolve', 'forget']);

    $component->assertSee('Resolve a1')->assertSee('/logs/a1');
});

test('running an action reloads the detail page', function () {
    $entry = new LogEntryFixture('a1');

    Livewire::test(Menu::class, ['subject' => $entry])
        ->call('run', 'resolve')
        ->assertRedirect();

    expect($entry->resolved)->toBeTrue();
});

test('a removed subject redirects to the index url of the set', function () {
    Livewire::test(Menu::class, ['subject' => new LogEntryFixture('a1')])
        ->call('run', 'forget')
        ->assertRedirect('/logs');
});

test('a denied action shows an alert instead of an error page', function () {
    Gate::define('resolve-log', fn ($user = null) => false);

    $entry = new LogEntryFixture('a1');

    Livewire::test(Menu::class, ['subject' => $entry])
        ->call('run', 'resolve')
        ->assertNoRedirect();

    expect($entry->resolved)->toBeFalse();
});

test('the action set cannot be swapped from the browser', function () {
    expect(fn () => Livewire::test(Menu::class, ['subject' => new LogEntryFixture('a1')])->set('set', ActionRunner::class))
        ->toThrow(Exception::class);
});
