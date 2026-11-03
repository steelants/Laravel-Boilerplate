<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Livewire\Component;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;

#[AllowInModal(asGuest: true)]
class ModalGuestContent extends Component
{
    public function render()
    {
        return '<div>guest content</div>';
    }
}
