<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;

/**
 * Statický vstup k ActionRunneru: vyhodnocení a spuštění akcí ze sady mimo DataTable a menu
 * (controller, služba, příkaz). V testu jde nahradit přes Actions::swap() / Actions::shouldReceive().
 *
 * @method static list<\SteelAnts\LaravelBoilerplate\Support\Actions\ActionItem> items(\SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet $set, object $subject)
 * @method static bool allows(\SteelAnts\LaravelBoilerplate\Support\Actions\Action $action, object $subject)
 * @method static \SteelAnts\LaravelBoilerplate\Support\Actions\ActionResult run(\SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet $set, object $subject, string $name)
 * @method static list<\SteelAnts\LaravelBoilerplate\Support\Actions\ActionItem> bulkItems(\SteelAnts\LaravelBoilerplate\Support\Actions\BulkActionSet $set)
 * @method static bool allowsBulk(\SteelAnts\LaravelBoilerplate\Support\Actions\BulkAction $action)
 * @method static \SteelAnts\DataTable\Support\BulkResult runBulk(\SteelAnts\LaravelBoilerplate\Support\Actions\BulkActionSet $set, list<string> $keys, string $name)
 *
 * @see ActionRunner
 */
class Actions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActionRunner::class;
    }
}
