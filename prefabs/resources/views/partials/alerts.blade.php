<div class="snackbar-container">
    @if ($message = Session::get('success'))
        <div class="snackbar alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                <i class="alert-ico far fa-check-circle text-success"></i>
                <div>
                    <div class="alert-title">{{ $message }}</div>
                </div>
            </div>
        </div>
    @endif

    @if ($messages = Session::get('errors') && isset(Session::get('errors')->toArray()['error']))
        @foreach ($messages->toArray()['error'] as $message)
            <div class="snackbar alert">
                <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

                <div class="alert-content">
                    <i class="alert-ico fas fa-times-circle text-danger"></i>
                    <div>
                        <div class="alert-title">{{ $message }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if ($message = Session::get('error'))
        <div class="snackbar alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                <i class="alert-ico fas fa-times-circle text-danger"></i>
                <div>
                    <div class="alert-title">{{ $message }}</div>
                </div>
            </div>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="snackbar alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                <i class="alert-ico fas fa-exclamation-triangle text-warning"></i>
                <div>
                    <div class="alert-title">{{ $message }}</div>
                </div>
            </div>
        </div>
    @endif

    @if ($message = Session::get('info'))
        <div class="snackbar alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                <i class="alert-ico fas fa-info-circle text-info"></i>
                <div>
                    <div class="alert-title">{{ $message }}</div>
                </div>
            </div>
        </div>
    @endif

    @if ($message = Session::get('message'))
     <div class="snackbar alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                <i class="alert-ico fas fa-info-circle text-info"></i>
                <div>
                    <div class="alert-title">{{ $message }}</div>
                </div>
            </div>
        </div>
    @endif
