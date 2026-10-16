<?php

namespace SteelAnts\LaravelBoilerplate\Attributes;

use Attribute;

/**
 * Oznacuje Livewire komponentu, kterou je mozne otevrit v modalu.
 *
 * Bez tohoto atributu modal komponentu neotevre.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class AllowInModal
{
    public function __construct(
        public bool $asGuest = false,
        public ?string $ability = null,
        public mixed $arguments = null,
    ) {
    }
}
