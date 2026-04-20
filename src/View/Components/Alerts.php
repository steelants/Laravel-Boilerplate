<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use SteelAnts\LaravelBoilerplate\Facades\Alert;
use SteelAnts\LaravelBoilerplate\Support\AlertItem;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class Alerts extends Component
{
    public array $alerts = [];

    public function __construct()
    {
        foreach (['success', 'error', 'warning', 'info', 'message'] as $key) {
            $message = session()->pull($key);
            if (!empty($message)) {
                $type = $key === 'message' ? 'info' : $key;
                $this->alerts[] = new AlertItem(type: $type, text: $message);
            }
        }

        $this->alerts = array_merge($this->alerts, Alert::get(AlertModeType::RELOAD));
        $this->alerts = array_merge($this->alerts, Alert::get(AlertModeType::INSTANT));

        Session::forget('alerts-' . AlertModeType::RELOAD);
        Session::forget('alerts-' . AlertModeType::INSTANT);
    }

    public function render(): View|string
    {
        return view('boilerplate::components.alerts', ['alerts' => $this->alerts]);
    }
}
