<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('Api Tokens') }}</h1>
            <div>
                @if (session()->has('secret'))
                    <code onclick="copyToClipboard('{{ session()->get('secret') }}');" title="{{ __('Copy to clipboard') }}">
                        {{ session()->get('secret') }}
                    </code>
                @else
                    <form action="{{ route('profile.api.create') }}" class="row row-cols-lg-auto g-3 align-items-center mb-3" method="post">
                        @csrf
                        <div class="col-12">
                            <label class="visually-hidden" for="token-name">{{ __('Name') }}</label>
                            <div class="input-group">
                                <input class="form-control" id="token-name" name="token_name" placeholder="{{ __('Name') }}" type="text">
                            </div>
                        </div>
                        <x-form::input class="form-control" type="date" name="expires_at" id="expires_at" min="{{ now()->toDateString('Y-m-d') }}" placeholder="{{ __('Expire at') }}" />
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
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
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Last used at') }}</th>
                        <th scope="col">{{ __('Expire at') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <th scope="row">{{ $token->name }}</th>
                            <td>{{ $token->last_used_at ??  __('Never')  }}</td>
                            <td>{{ $token->expires_at ??  __('Never') }}</td>
                            <td>
                                <form action="{{ route('profile.api.remove', ['token_id' => $token->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <input class="btn btn-danger" type="submit" value="{{ __('Remove') }}" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout-app>
