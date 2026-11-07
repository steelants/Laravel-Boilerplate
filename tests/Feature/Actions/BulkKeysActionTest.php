<?php

use SteelAnts\DataTable\Support\BulkResult;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkAction;
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkActionSet;

// Hromadná akce keys() - obsluha dostane jen pole klíčů výběru a předá ho službě, nic se nenačítá.

/** Služba, kterou keys() akce dostane z DI containeru. */
class BulkKeysServiceFixture
{
    /** @var list<string> */
    public array $received = [];

    public function archive(array $keys): int
    {
        $this->received = $keys;

        return count($keys) - 1;
    }
}

function keysBulkSet(BulkAction $action): BulkActionSet
{
    return new class ($action) extends BulkActionSet
    {
        public function __construct(private BulkAction $action)
        {
        }

        protected function define(): array
        {
            return [$this->action];
        }

        public function subjects(array $keys): iterable
        {
            throw new LogicException('keys() actions must not load subjects.');
        }
    };
}

test('keys() hands the keys to the service without loading subjects', function () {
    app()->instance(BulkKeysServiceFixture::class, $service = new BulkKeysServiceFixture());
    $set = keysBulkSet(BulkAction::make('archive')->keys(fn (array $keys, BulkKeysServiceFixture $service) => $service->archive($keys)));

    $result = app(ActionRunner::class)->runBulk($set, ['1', '2', '3'], 'archive');

    expect($service->received)->toBe(['1', '2', '3'])
        ->and($result->ok)->toBe(2);
});

test('keys() counts all keys as processed unless the service reports otherwise', function () {
    $counted = keysBulkSet(BulkAction::make('a')->keys(fn (array $keys) => null));
    $reported = keysBulkSet(BulkAction::make('a')->keys(fn (array $keys) => new BulkResult(1, 1, 0, ['Locked' => 1])));

    expect(app(ActionRunner::class)->runBulk($counted, ['1', '2'], 'a')->ok)->toBe(2)
        ->and(app(ActionRunner::class)->runBulk($reported, ['1', '2'], 'a')->reasons)->toBe(['Locked' => 1]);
});

test('keys() cannot be combined with per-object checks or another handler', function (BulkAction $action) {
    keysBulkSet($action)->actions();
})->with([
    'can'        => fn () => BulkAction::make('a')->can('update')->keys(fn () => 0),
    'applicable' => fn () => BulkAction::make('a')->applicable(fn () => true, 'x')->keys(fn () => 0),
    'each'       => fn () => BulkAction::make('a')->each(fn () => null)->keys(fn () => 0),
])->throws(LogicException::class);
