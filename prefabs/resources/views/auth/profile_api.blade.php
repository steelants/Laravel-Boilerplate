<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.api_tokens') }}</h1>
            <div>
                @if (session()->has('secret'))
                    <code>{{ session()->get('secret') }}</code>
                @else
                    <form action="{{ route('profile.api.create') }}" class="row row-cols-lg-auto g-3 align-items-center mb-3" method="post">
                        @csrf
                        <div class="col-12">
                            <label class="visually-hidden" for="token-name">{{ __('boilerplate::ui.name') }}</label>
                            <div class="input-group">
                                <input class="form-control" id="token-name" name="token_name" placeholder="{{ __('boilerplate::ui.name') }}" type="text">
                            </div>
                        </div>
                        <x-form::input class="form-control" type="date" name="expires_at" id="expires_at" min="{{ now()->toDateString('Y-m-d') }}" placeholder="{{ __('boilerplate::ui.expires_at') }}" />
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">{{ __('boilerplate::ui.create') }}</button>
                        </div>
                        <div class="col-12">
                            @error('token_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            @error('expire_at')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('boilerplate::ui.name') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.last_used_at') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.expires_at') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <th scope="row">{{ $token->name }}</th>
                            <td>{{ $token->last_used_at ??  __('boilerplate::ui.never')  }}</td>
                            <td>{{ $token->expires_at ??  __('boilerplate::ui.never') }}</td>
                            <td>
                                <form action="{{ route('profile.api.remove', ['token_id' => $token->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <input class="btn btn-danger" type="submit" value="{{ __('boilerplate::ui.remove') }}" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout-app>
