<div>
    <x-form livewireAction="store" method="post">
        <x-form-input groupClass="mb-3" id="name" livewireModel="name" name="name" value="{{ old('name') }}" label="{{ __('boilerplate::ui.name') }}" />
        <x-form-input groupClass="mb-3" id="email" livewireModel="email" name="email" type="email" value="{{ old('email') }}" label="{{ __('boilerplate::ui.email') }}" />
        <x-form-input groupClass="mb-3" id="password" livewireModel="password" name="password" type="password" value="{{ old('password') }}" label="{{ __('boilerplate::ui.password') }}" />
        <x-form-input groupClass="mb-3" id="password_confirmation" livewireModel="password_confirmation" name="password_confirmation" type="password"  value="{{ old('password') }}" label="{{ __('boilerplate::ui.confirm.password') }}" />
        <x-form-submit>{{ __('boilerplate::ui.create') }}</x-form-submit>
    </x-form>
</div>