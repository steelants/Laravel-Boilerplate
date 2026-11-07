<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use SteelAnts\LaravelBoilerplate\Exceptions\ActionException;

/**
 * Překlad výjimek ze služeb na alert místo chybové stránky (403/500).
 * V Livewire requestu se alert zobrazí hned (snackbar), jinak po přesměrování (reload).
 */
trait HandlesActionErrors
{
    /** Provede operaci; při odmítnutí zobrazí alert a vrátí false. */
    protected function attempt(callable $operation): bool
    {
        try {
            $operation();
        } catch (AuthorizationException) {
            if (request()->hasHeader('X-Livewire')) {
                alert()->error(__('You are not authorized to perform this action.'))->now();
            } else {
                alert()->error(__('You are not authorized to perform this action.'))->reload();
            }

            return false;
        } catch (ActionException $e) {
            $this->recordActionAlert($e);

            return false;
        }

        return true;
    }

    private function recordActionAlert(ActionException $e): void
    {
        // TODO: opravit html alerty - AlertBuilder neumí HTML/details, proto přímý dispatch snackbaru (jen Livewire).
        if ($e->details !== null && method_exists($this, 'dispatch')) {
            $this->dispatch('snackbar', [
                'message' => $e->getMessage(),
                'details' => $e->details,
                'type'    => 'danger',
                'icon'    => $e->icon,
            ]);

            return;
        }

        if (request()->hasHeader('X-Livewire')) {
            alert()->error($e->getMessage(), $e->icon)->now();
        } else {
            alert()->error($e->getMessage(), $e->icon)->reload();
        }
    }
}
