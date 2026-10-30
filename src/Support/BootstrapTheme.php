<?php

namespace SteelAnts\LaravelBoilerplate\Support;

/**
 * Components keep accepting the Bootstrap 5 color and button variant names
 * (primary, light, dark, outline-secondary, ...) and render Bootstrap 6 classes.
 */
final class BootstrapTheme
{
    public static function theme(string $color): string
    {
        return match ($color) {
            'light' => 'secondary',
            'dark'  => 'inverse',
            'error' => 'danger',
            default => $color,
        };
    }

    public static function button(string $variant): string
    {
        return match (true) {
            $variant === 'link'                  => 'btn-link',
            $variant === 'light'                 => 'btn-subtle theme-secondary',
            str_starts_with($variant, 'outline-') => 'btn-outline theme-' . self::theme(substr($variant, strlen('outline-'))),
            default                              => 'btn-solid theme-' . self::theme($variant),
        };
    }
}
