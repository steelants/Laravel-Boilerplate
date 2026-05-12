<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Facades\Session;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class AlertCollector
{
    protected static array $pending = [];

    public function add(string $type, string $text, string $icon = '', int $mode = AlertModeType::RELOAD, bool $persist = false): void
    {
        $item = new AlertItem(type: $type, text: $text, icon: $icon, persist: $persist);

        if ($mode === AlertModeType::INSTANT && request()->hasHeader('X-Livewire')) {
            $this->addPending($item);
        } else {
            $this->addItem($item, $mode);
        }
    }

    public function addItem(AlertItem $item, int $mode): void
    {
        $alerts = Session::get('alerts-' . $mode, []);
        $alerts[] = $item;

        $mode === AlertModeType::RELOAD ? Session::flash('alerts-' . $mode, $alerts) : Session::now('alerts-' . $mode, $alerts);
    }

    public function addPending(AlertItem $item): void
    {
        static::$pending[] = $item;
    }

    public function takePending(): array
    {
        $pending = static::$pending;
        static::$pending = [];

        return $pending;
    }

    public function get(int $mode = AlertModeType::RELOAD): array
    {
        return Session::get('alerts-' . $mode, []);
    }
}
