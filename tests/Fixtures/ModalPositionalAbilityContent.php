<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Livewire\Component;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;

// Pozicni zapis zamerne - modal ho musi vylozit jako ability.
#[AllowInModal('is-modal-admin')]
class ModalPositionalAbilityContent extends Component
{
    public function render()
    {
        return '<div>admin content</div>';
    }
}
