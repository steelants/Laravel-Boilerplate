<div>
    <form wire:submit.prevent="store" method="post">
        <x-form-input id="name" livewireModel="name" name="name" value="{{ old('name') }}" label="{{ __('boilerplate::ui.name') }}" />
        <x-form-input id="email" livewireModel="email" name="email" type="email" value="{{ old('email') }}" label="{{ __('boilerplate::ui.email') }}" />
        <x-form-input id="password" livewireModel="password" name="password" type="password" value="{{ old('password') }}" label="{{ __('boilerplate::ui.password') }}" />
        <x-form-input id="password_confirmation" livewireModel="password_confirmation" name="password_confirmation" type="password"  value="{{ old('password') }}" label="{{ __('boilerplate::ui.confirm.password') }}" />
        <x-form-submit text="{{ __('boilerplate::ui.create') }}" />
    </form>
</div>