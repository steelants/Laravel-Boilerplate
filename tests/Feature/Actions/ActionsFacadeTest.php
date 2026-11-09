<?php

use Illuminate\Support\Facades\Gate;
use SteelAnts\LaravelBoilerplate\Facades\Actions;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionResult;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryActions;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryFixture;

require_once __DIR__ . '/../../Fixtures/ActionsFixture.php';

// Fasáda Actions je jen statický vstup k ActionRunneru z containeru.

beforeEach(function () {
    LogEntryFixture::$store = [];
    Gate::define('resolve-log', fn ($user = null) => true);
});

test('the facade runs actions through the container runner', function () {
    $entry = new LogEntryFixture('a1');

    expect(Actions::getFacadeRoot())->toBeInstanceOf(ActionRunner::class)
        ->and(Actions::getFacadeRoot())->toBe(app(ActionRunner::class));

    Actions::run(app(LogEntryActions::class), $entry, 'resolve');

    expect($entry->resolved)->toBeTrue();
});

test('the facade can be mocked in tests', function () {
    $entry = new LogEntryFixture('a1');
    $set = app(LogEntryActions::class);

    Actions::shouldReceive('run')->once()->with($set, $entry, 'resolve')->andReturn(ActionResult::refresh());

    Actions::run($set, $entry, 'resolve');

    expect($entry->resolved)->toBeFalse();
});
