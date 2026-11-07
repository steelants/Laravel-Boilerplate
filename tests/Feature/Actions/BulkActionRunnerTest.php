<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryBulkActions;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\LogEntryFixture;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__ . '/../../Fixtures/ActionsFixture.php';

// Hromadné akce nad výběrem klíčů - každý objekt prochází applicable() a can() zvlášť.

beforeEach(function () {
    LogEntryFixture::$store = [];
    $this->canManage = true;
    $this->canResolve = fn (?LogEntryFixture $entry) => true;
    Gate::define('manage-logs', fn ($user = null) => $this->canManage);
    Gate::define('resolve-log', fn ($user = null, ?LogEntryFixture $entry = null) => ($this->canResolve)($entry));
    $this->runner = app(ActionRunner::class);
    $this->set = app(LogEntryBulkActions::class);
});

test('bulk items are offered only when their gate allows it', function () {
    expect(array_map(fn ($item) => $item->name, $this->runner->bulkItems($this->set)))->toBe(['resolve', 'resolveAll']);

    $this->canManage = false;

    expect(array_map(fn ($item) => $item->name, $this->runner->bulkItems($this->set)))->toBe(['resolveAll'])
        ->and(fn () => $this->runner->runBulk($this->set, [], 'resolve'))->toThrow(AuthorizationException::class)
        ->and(fn () => $this->runner->runBulk($this->set, [], 'nope'))->toThrow(NotFoundHttpException::class);
});

test('each() counts processed, skipped with reasons and failed objects', function () {
    $ok = new LogEntryFixture('ok');
    new LogEntryFixture('done', resolved: true);
    new LogEntryFixture('locked', level: 'locked');
    new LogEntryFixture('broken', level: 'broken');
    new LogEntryFixture('denied');
    $this->canResolve = fn (?LogEntryFixture $entry) => $entry?->id !== 'denied';

    $result = $this->runner->runBulk($this->set, ['ok', 'done', 'locked', 'broken', 'denied', 'missing'], 'resolve');

    expect($ok->resolved)->toBeTrue()
        ->and($result->ok)->toBe(1)
        ->and($result->skipped)->toBe(3)
        ->and($result->failed)->toBe(1)
        ->and($result->reasons)->toBe([
            'Already resolved'                               => 1,
            'Entry is locked'                                => 1,
            'You are not authorized to perform this action.' => 1,
        ]);
});

test('handle() gets one collection of the objects that passed', function () {
    $open = [new LogEntryFixture('a'), new LogEntryFixture('b')];
    new LogEntryFixture('done', resolved: true);

    $result = $this->runner->runBulk($this->set, ['a', 'b', 'done'], 'resolveAll');

    expect(array_map(fn ($entry) => $entry->resolved, $open))->toBe([true, true])
        ->and($result->ok)->toBe(2)
        ->and($result->skipped)->toBe(1)
        ->and($result->reasons)->toBe(['Already resolved' => 1]);
});
