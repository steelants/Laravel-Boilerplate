<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Support\Collection;
use SteelAnts\LaravelBoilerplate\Attributes\UseActions;
use SteelAnts\LaravelBoilerplate\Exceptions\ActionException;
use SteelAnts\LaravelBoilerplate\Support\Actions\Action;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet;
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkAction;
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkActionSet;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** Objekt, který není Eloquent model (záznam logu) - akce nad ním drží LogEntryActions. */
#[UseActions(LogEntryActions::class)]
class LogEntryFixture
{
    /** @var array<string, self> */
    public static array $store = [];

    public function __construct(
        public string $id,
        public bool $resolved = false,
        public string $level = 'error',
    ) {
        self::$store[$id] = $this;
    }
}

/** Služba, kterou akce dostanou z DI containeru. */
class LogEntryServiceFixture
{
    public function resolve(LogEntryFixture $entry): void
    {
        if ($entry->level === 'locked') {
            throw new ActionException('Entry is locked');
        }

        if ($entry->level === 'broken') {
            throw new \RuntimeException('Broken entry');
        }

        $entry->resolved = true;
    }

    /** @param Collection<int, LogEntryFixture> $entries */
    public function resolveAll(Collection $entries): void
    {
        $entries->each(fn (LogEntryFixture $entry) => $entry->resolved = true);
    }
}

class LogEntryActions extends ActionSet
{
    protected function define(): array
    {
        return [
            Action::make('show')
                ->label('Show')
                ->icon('fas fa-eye')
                ->url(fn (LogEntryFixture $entry) => '/logs/' . $entry->id),

            Action::make('resolve')
                ->label(fn (LogEntryFixture $entry) => 'Resolve ' . $entry->id)
                ->icon('fas fa-check')
                ->confirm('Sure?')
                ->visible(fn (LogEntryFixture $entry) => !$entry->resolved)
                ->can('resolve-log')
                ->handle(fn (LogEntryFixture $entry, LogEntryServiceFixture $service) => $service->resolve($entry)),

            Action::remove(function (LogEntryFixture $entry): void {
                unset(LogEntryFixture::$store[$entry->id]);
            })->confirm('Forget?'),

            // Metoda komponenty (modal) - jen DataTable, menu v detailu ji vynechá.
            Action::make('inspect')
                ->label('Inspect')
                ->icon('fas fa-bug')
                ->livewire('inspect'),

            // Přepínač ve sloupci (RenderCasts\ToggleAction) - spustitelný, ale v menu se nenabízí.
            Action::make('toggleResolved')
                ->unlisted()
                ->handle(fn (LogEntryFixture $entry) => $entry->resolved = !$entry->resolved),
        ];
    }

    public function resolve(string $key): LogEntryFixture
    {
        return LogEntryFixture::$store[$key] ?? throw new NotFoundHttpException();
    }

    public function key(object $subject): string
    {
        return $subject->id;
    }

    public function indexUrl(object $subject): string
    {
        return '/logs';
    }
}

class LogEntryBulkActions extends BulkActionSet
{
    protected function define(): array
    {
        return [
            BulkAction::make('resolve')
                ->label('Resolve')
                ->icon('fas fa-check')
                ->authorize('manage-logs')
                ->can('resolve-log')
                ->applicable(fn (LogEntryFixture $entry) => !$entry->resolved, 'Already resolved')
                ->each(fn (LogEntryFixture $entry, LogEntryServiceFixture $service) => $service->resolve($entry)),

            BulkAction::make('resolveAll')
                ->label('Resolve all')
                ->icon('fas fa-check-double')
                ->applicable(fn (LogEntryFixture $entry) => !$entry->resolved, 'Already resolved')
                ->handle(fn (Collection $subjects, LogEntryServiceFixture $service) => $service->resolveAll($subjects)),
        ];
    }

    public function subjects(array $keys): iterable
    {
        return array_values(array_intersect_key(LogEntryFixture::$store, array_flip($keys)));
    }
}

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
