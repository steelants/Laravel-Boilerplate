<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

/**
 * Akce vyhodnocená pro konkrétní objekt a uživatele - to, co se vykreslí v řádku DataTable i v menu detailu.
 * Destruktivní akce má vždy červený text i ikonu, styl odvozuje položka, ne volající.
 */
final readonly class ActionItem
{
    public function __construct(
        public string $name,
        public string $text,
        public string $icon,
        public ?string $url = null,
        public ?string $confirm = null,
        public bool $destructive = false,
    ) {
    }

    public function isLink(): bool
    {
        return $this->url !== null;
    }

    public function iconClass(): string
    {
        return $this->destructive ? $this->icon . ' text-danger' : $this->icon;
    }

    public function actionClass(): ?string
    {
        return $this->destructive ? 'text-danger' : null;
    }
}
