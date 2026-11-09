<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

/** Co má zobrazení udělat po provedení akce (DataTable i menu v detailu). */
final readonly class ActionResult
{
    private function __construct(
        public ?string $redirect = null,
        public bool $removed = false,
    ) {
    }

    /** Výchozí - zobrazení se obnoví (řádek DataTable, znovunačtení detailu). */
    public static function refresh(): self
    {
        return new self();
    }

    public static function redirect(string $url): self
    {
        return new self(redirect: $url);
    }

    /** Objekt zanikl: DataTable jen obnoví řádky, detail přejde na ActionSet::indexUrl(). */
    public static function removed(): self
    {
        return new self(removed: true);
    }
}
