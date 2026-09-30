<?php

use Illuminate\Support\Facades\Blade;
use Livewire\LivewireServiceProvider;
use SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider;

beforeEach(function () {
    $this->app->register(LivewireServiceProvider::class);
    $this->app->register(BoilerplateServiceProvider::class);
});

it('renders action button with loading state scoped to its target', function () {
    $html = Blade::render('<x-boilerplate::action-button action="remove(5)" icon="fas fa-trash" confirm="Sure?" variant="danger">Delete</x-boilerplate::action-button>');

    expect($html)
        ->toContain('wire:click="remove(5)"')
        ->toContain('wire:confirm="Sure?"')
        ->toContain('wire:loading.attr="disabled"')
        ->toContain('wire:target="remove(5)"')
        ->toContain('btn-danger')
        ->toContain('spinner-border');
});

it('takes target from wire:click and does not duplicate it', function () {
    $html = Blade::render('<x-boilerplate::action-button wire:click="save">Save</x-boilerplate::action-button>');

    expect(substr_count($html, 'wire:click="save"'))->toBe(1)
        ->and($html)->toContain('wire:target="save"');
});

it('wraps disabled action button in tooltip with reason', function () {
    $html = Blade::render('<x-boilerplate::action-button action="send" :disabled="true" disabled-reason="No recipients">Send</x-boilerplate::action-button>');

    expect($html)
        ->toContain('title="No recipients"')
        ->toContain('tabindex="0"')
        ->toContain('disabled aria-disabled="true"')
        ->not->toContain('wire:loading.attr');
});

it('renders alert with title, actions and close button', function () {
    $html = Blade::render(<<<'BLADE'
        <x-boilerplate::alert type="error" title="Failed" dismissible>
            Something went wrong.
            <x-slot:actions><a href="#">Retry</a></x-slot:actions>
        </x-boilerplate::alert>
    BLADE);

    expect($html)
        ->toContain('alert-danger')
        ->toContain('fas fa-times-circle')
        ->toContain('Failed')
        ->toContain('Something went wrong.')
        ->toContain('flex-shrink-0')
        ->toContain('data-bs-dismiss="alert"');
});

it('renders icon tile, stat and empty state', function () {
    expect(Blade::render('<x-boilerplate::icon-tile icon="fas fa-user" color="success" />'))
        ->toContain('bg-success-subtle')
        ->toContain('text-success-emphasis')
        ->toContain('w-10 h-10');

    expect(Blade::render('<x-boilerplate::stat label="Users" value="42" icon="fas fa-user" color="green" />'))
        ->toContain('stat-ico is-green')
        ->toContain('42');

    expect(Blade::render('<x-boilerplate::empty-state title="Nothing here" description="Add first item" />'))
        ->toContain('bg-secondary-subtle')
        ->toContain('Nothing here');
});

it('renders determinate and indeterminate progress', function () {
    expect(Blade::render('<x-boilerplate::progress :value="25" :max="50" label="Import" />'))
        ->toContain('width: 50%')
        ->toContain('aria-valuenow="50"')
        ->toContain('50&nbsp;%');

    expect(Blade::render('<x-boilerplate::progress label="Waiting" />'))
        ->toContain('progress-bar-animated')
        ->not->toContain('aria-valuenow');
});

it('renders dropdown with items and divider', function () {
    $html = Blade::render(<<<'BLADE'
        <x-boilerplate::dropdown>
            <x-boilerplate::dropdown-item href="/edit" icon="fas fa-pen">Edit</x-boilerplate::dropdown-item>
            <x-boilerplate::dropdown-divider />
            <x-boilerplate::dropdown-item wire:click="remove" icon="fas fa-trash" danger>Delete</x-boilerplate::dropdown-item>
        </x-boilerplate::dropdown>
    BLADE);

    expect($html)
        ->toContain('dropdown-menu dropdown-menu-end')
        ->toContain('href="/edit"')
        ->toContain('dropdown-divider')
        ->toContain('wire:click="remove"')
        ->toContain('text-danger');
});

it('renders detail list from items and slot', function () {
    $html = Blade::render(<<<'BLADE'
        <x-boilerplate::detail-list :items="['Name' => 'John']">
            <x-boilerplate::detail-list.item label="Note" />
        </x-boilerplate::detail-list>
    BLADE);

    expect($html)
        ->toContain('list-group-flush')
        ->toContain('>Name</dt>')
        ->toContain('John')
        ->toContain('&mdash;');
});

it('renders relative time with exact tooltip', function () {
    $html = Blade::render('<x-boilerplate::time :datetime="now()->subHour()" />');

    expect($html)
        ->toContain('datetime="')
        ->toContain('1 hour ago')
        ->toContain('x-data="bsTooltip()"');

    expect(Blade::render('<x-boilerplate::time :datetime="null" />'))->toContain('—');
});

it('renders copy button and nav switch', function () {
    expect(Blade::render('<x-boilerplate::copy value="secret">secret</x-boilerplate::copy>'))
        ->toContain('copyToClipboard(')
        ->toContain('text-truncate');

    $html = Blade::render('<x-boilerplate::nav-switch wire:model.live="view" :options="[\'list\' => \'List\', \'grid\' => [\'label\' => \'Grid\', \'icon\' => \'fas fa-th\']]" />');

    expect($html)
        ->toContain('type="radio"')
        ->toContain('wire:model.live="view"')
        ->toContain('name="view"')
        ->toContain('fas fa-th');
});

it('renders tab group bound to livewire property with badge and scroll', function () {
    $html = Blade::render(<<<'BLADE'
        <x-boilerplate::tab.group wire:model.live="tab" default="a" query="tab" scroll>
            <x-boilerplate::tab.tabs scroll>
                <x-boilerplate::tab.tab name="a" badge="3">A</x-boilerplate::tab.tab>
            </x-boilerplate::tab.tabs>
            <x-boilerplate::tab.panel name="a">Panel</x-boilerplate::tab.panel>
        </x-boilerplate::tab.group>
    BLADE);

    expect($html)
        ->toContain("\$wire.entangle(")
        ->toContain('.live')
        ->toContain('history.replaceState')
        ->toContain('flex-nowrap overflow-x-auto')
        ->toContain("'mb-0': scroll")
        ->toContain('badge rounded-pill')
        ->not->toContain('wire:model.live="tab"');
});
