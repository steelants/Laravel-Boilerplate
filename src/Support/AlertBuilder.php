<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class AlertBuilder
{
    protected string $type = 'info';

    protected string $text = '';

    protected string $icon = '';

    protected bool $shouldPersist = false;

    public function type(string $type = 'info', string $text = '', string $icon = ''): static
    {
        $this->type = $type;
        if ($text !== '') {
            $this->text = $text;
        }
        if ($icon !== '') {
            $this->icon = $icon;
        }

        return $this;
    }

    public function success(string $text, string $icon = 'fas fa-check'): static
    {
        return $this->type('success', $text, $icon);
    }

    public function error(string $text, string $icon = 'fas fa-times'): static
    {
        return $this->type('error', $text, $icon);
    }

    public function warning(string $text, string $icon = 'fas fa-exclamation-triangle'): static
    {
        return $this->type('warning', $text, $icon);
    }

    public function info(string $text, string $icon = 'fas fa-info-circle'): static
    {
        return $this->type('info', $text, $icon);
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function persist(bool $persist = true): static
    {
        $this->shouldPersist = $persist;

        return $this;
    }

    public function now(): void
    {
        $collector = app(AlertCollector::class);
        $item = new AlertItem($this->type, $this->text, $this->icon, $this->shouldPersist);

        if (request()->hasHeader('X-Livewire')) {
            $collector->addPending($item);
        } else {
            $collector->addItem($item, AlertModeType::INSTANT);
        }
    }

    public function reload(): void
    {
        app(AlertCollector::class)->addItem(
            new AlertItem($this->type, $this->text, $this->icon, $this->shouldPersist),
            AlertModeType::RELOAD
        );
    }
}
