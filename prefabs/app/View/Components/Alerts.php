<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SteelAnts\LaravelBoilerplate\Facades\Alert;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class Alerts extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?array $alerts = [],
    )
    {
        $types = [
            'success',
            'error',
            'warning',
            'info',
            'message',
        ];

        foreach($types as $type){
            $message = session()->get($type);
            if(!empty($message)){
                $this->alerts[] = [
                    'type' => $type,
                    'message' => $message
                ];
            }
        }

        if(session()->has('errors')){
            $items = session()->get('errors')->toArray();
            foreach($items as $item){
                if(!empty($item['error'])){
                    $this->alerts[] = [
                        'type' => 'error',
                        'message' => $item['error']
                    ];
                }
            }
        }

		$this->alerts = array_merge($this->alerts, Alert::get(AlertModeType::INSTANT));
		$this->alerts = array_merge($this->alerts, Alert::get(AlertModeType::RELOAD));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts');
    }
}
