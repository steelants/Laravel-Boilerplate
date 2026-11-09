<?php

namespace SteelAnts\LaravelBoilerplate\Exceptions;

use RuntimeException;

/**
 * Doménové odmítnutí operace ze služby (např. výchozího dodavatele nelze smazat,
 * na záznam navazují doklady). HandlesActionErrors ji zobrazí jako alert, ne jako 500.
 * $details = HTML doplněk (seznam navázaných záznamů), ten umí jen Livewire snackbar.
 */
class ActionException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?string $details = null,
        public readonly string $icon = 'fas fa-exclamation-triangle',
    ) {
        parent::__construct($message);
    }
}
