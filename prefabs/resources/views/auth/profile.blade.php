<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.profile') }}</h1>
        </div>

        <div class="form-group">
            <label class="form-label"><b>{{ __('boilerplate::ui.name') }}:</b></label>
            <p>{{ $user->name ?? '' }}</p>
        </div>

        <div class="form-group">
            <label class="form-label"><b>{{ __('boilerplate::ui.email') }}:</b></label>
            <p>{{ $user->email ?? '' }}</p>
        </div>

        <hr/>
        @livewire('user.limitation', ['user' => $user])
        <hr/>

        @if (config('session.driver') == 'database')
            <div>
                <h4>{{ __('boilerplate::ui.sessions') }}</h4>
                @livewire('session.data-table', [], key('data-table'))
            </div>
        @endif

        <div>
            <h4>{{ __('boilerplate::ui.change_password') }}</h4>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('put')
            <div class="form-group">
                <label class="form-label" for="password">{{ __('boilerplate::ui.old.password') }}</label>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="newPassword">{{ __('boilerplate::ui.new.password') }}</label>
                <input class="form-control @error('newPassword') is-invalid @enderror" id="newPassword" name="newPassword" type="password">

                @error('newPassword')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="newPassword-confirm">{{ __('boilerplate::ui.confirm.password') }}</label>
                <input class="form-control" id="newPassword-confirm" name="newPassword_confirmation" type="password">
            </div>

            <button class="btn btn-primary" type="submit">{{ __('boilerplate::ui.update') }}</button>
        </form>
    </div>
</x-layout-app>
