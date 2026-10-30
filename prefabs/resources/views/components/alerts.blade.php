<div class="snackbar-container">
    @foreach ($alerts as $alert)
        <div class="snackbar {{ !empty($alert->persist) ? 'snackbar-persist' : ''}} alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                @switch($alert->type)
                    @case('success')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fa-regular fa-check-circle' }} fg-success"></i>
                        @break
                    @case('error')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fa-solid fa-times-circle' }} fg-danger"></i>
                        @break
                    @case('warning')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fa-solid fa-exclamation-triangle' }} fg-warning"></i>
                        @break
                    @default
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fa-solid fa-info-circle' }} fg-info"></i>
                @endswitch
                <div>
                    <div class="alert-title">{{ $alert->text }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
