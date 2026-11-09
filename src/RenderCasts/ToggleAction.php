<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use Illuminate\Support\Str;
use SteelAnts\DataTable\RenderCasts\RenderCast;

/**
 * Bool sloupec, který se kliknutím přepne akcí ze sady DataTable (WithActions::runAction).
 * Akce se odvodí z klíče sloupce: is_active -> toggleIsActive, totp_force -> toggleTotpForce;
 * v sadě bývá ->unlisted(), aby se nenabízela i v menu řádku.
 *
 *     public function renderCasts(): array
 *     {
 *         return ['is_active' => ToggleAction::class];
 *     }
 */
class ToggleAction implements RenderCast
{
    public function render($key, $value, $model)
    {
        // JSON literál escapovaný pro HTML atribut - bezpečný i pro textové klíče (session ID).
        $call = json_encode(['toggle' . Str::studly($key), (string) data_get($model, 'id')], JSON_THROW_ON_ERROR);

        return '<button type="button" class="btn btn-link p-0" wire:click="runAction(' . e($call) . ')">'
            . app(BoolAsIcon::class)->render($key, $value, $model)
            . '</button>';
    }
}
