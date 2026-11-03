<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider;
use SteelAnts\LaravelBoilerplate\Livewire\Modal\Basic;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\ModalGuestContent;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\ModalPositionalAbilityContent;

beforeEach(function () {
    // Livewire sifruje snapshot komponenty, testbench app key nema.
    config()->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
    $this->app->register(LivewireServiceProvider::class);
    $this->app->register(BoilerplateServiceProvider::class);

    Livewire::component('modal-guest-content', ModalGuestContent::class);
    Livewire::component('modal-positional-ability-content', ModalPositionalAbilityContent::class);
});

it('rejects invalid openModal arguments', function (array $arguments, string $message) {
    expect(fn () => Livewire::test(Basic::class)->call('openModal', 'modal-guest-content', ...$arguments))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'size as array'    => [['Title', [], ['lg']], 'Modal size must be one of sm, md, lg, xl, array given (components: "modal-guest-content").'],
    'unknown size'     => [['Title', [], 'huge'], "Modal size must be one of sm, md, lg, xl, 'huge' given"],
    'title as array'   => [[['Edit', 'user']], 'Modal title must be a string, array given'],
    'static as int'    => [['Title', [], 'md', 0], 'Modal static must be a bool, int given'],
    'static as string' => [['Title', [], 'md', 'false'], 'Modal static must be a bool, string given'],
]);

it('does not open the modal when arguments are invalid', function () {
    $component = Livewire::test(Basic::class);

    try {
        $component->call('openModal', 'modal-guest-content', 'Title', [], ['lg']);
    } catch (InvalidArgumentException) {
    }

    $component->assertSet('_livewireComponents', [])->assertSet('size', null);
});

it('turns a null title into an empty string', function () {
    Livewire::test(Basic::class)
        ->call('openModal', 'modal-guest-content', null)
        ->assertSet('title', '');
});

it('keeps valid values untouched', function () {
    Livewire::test(Basic::class)
        ->call('openModal', 'modal-guest-content', 'Original title', [], 'lg', false)
        ->assertSet('title', 'Original title')
        ->assertSet('size', 'lg')
        ->assertSet('static', false)
        ->assertSeeHtml('modal-lg')
        ->assertSee('Original title')
        ->assertSee('guest content');
});

it('accepts named openModal arguments', function () {
    Livewire::test(Basic::class)
        ->dispatch('openModal', livewireComponents: 'modal-guest-content', title: 'Named', size: 'xl')
        ->assertSet('title', 'Named')
        ->assertSeeHtml('modal-xl');
});

it('refuses client updates of locked properties', function (string $property, mixed $value) {
    Livewire::test(Basic::class)->set($property, $value);
})->with([
    'size'   => ['size', ['lg']],
    'static' => ['static', false],
])->throws(CannotUpdateLockedPropertyException::class);

it('accepts a client title update', function () {
    Livewire::test(Basic::class)
        ->call('openModal', 'modal-guest-content', 'Old title')
        ->set('title', 'New title')
        ->assertSet('title', 'New title')
        ->assertSee('New title');
});

it('rejects an array title sent by the client', function () {
    expect(fn () => Livewire::test(Basic::class)
        ->call('openModal', 'modal-guest-content', 'Old title')
        ->set('title', ['New', 'title']))
        ->toThrow(InvalidArgumentException::class, 'Modal title must be a string, array given');
});

it('treats a positional AllowInModal argument as ability', function () {
    Gate::define('is-modal-admin', fn ($user) => $user->name === 'admin');

    Livewire::test(Basic::class)
        ->call('openModal', 'modal-positional-ability-content', 'Admin')
        ->assertSet('_livewireComponents', []);

    Livewire::actingAs((new User())->forceFill(['name' => 'user']))
        ->test(Basic::class)
        ->call('openModal', 'modal-positional-ability-content', 'Admin')
        ->assertSet('_livewireComponents', []);

    Livewire::actingAs((new User())->forceFill(['name' => 'admin']))
        ->test(Basic::class)
        ->call('openModal', 'modal-positional-ability-content', 'Admin')
        ->assertSet('_livewireComponents', 'modal-positional-ability-content')
        ->assertSee('admin content');
});
