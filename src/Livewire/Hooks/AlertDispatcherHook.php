<?php

namespace SteelAnts\LaravelBoilerplate\Livewire\Hooks;

use Livewire\ComponentHook;
use SteelAnts\LaravelBoilerplate\Support\AlertCollector;

class AlertDispatcherHook extends ComponentHook
{
    public function dehydrate($context): void
    {
        foreach (app(AlertCollector::class)->takePending() as $alert) {
            $this->component->dispatch('snackbar', [
                'type'    => $alert->type,
                'message' => $alert->text,
                'icon'    => $alert->icon,
            ]);
        }
    }
}
