<x-layout-auth>
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Send email') }}
                        </div>
                    @endif

                    {{ __('Check email') }}
                    {{ __('Not receive email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Request new') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout-auth>
