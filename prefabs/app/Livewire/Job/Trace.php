<?php

namespace App\Livewire\Job;

use Livewire\Component;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

#[AllowInModal('is-system-admin')]
class Trace extends Component
{
    public $exception = '';

    public function mount($job_uuid)
    {
       $this->exception = FailedJob::where('uuid', $this->job_uuid)->first()?->exception ?? '';
    }

    public function render()
    {
        return <<<'blade'
            <pre>{{ $exception }}</pre>
        blade;
    }
}
