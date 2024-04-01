<x-layout-auth>
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.Verify') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.sendEmail') }}
                        </div>
                    @endif

                    {{ __('auth.checkEmail') }}
                    {{ __('auth.notReceiveEmail') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('auth.requestNew') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout-auth>
