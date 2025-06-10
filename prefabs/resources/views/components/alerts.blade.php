<div class="snackbar-container">
    @foreach ($alerts as $alert)
        <div class="{{ !empty($alert->persist) ? 'snackbar-persist' : 'snackbar'}} alert">
            <button type="button" class="btn-close close" data-bs-dismiss="alert"></button>

            <div class="alert-content">
                @switch($alert->type)
                    @case('success')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'far fa-check-circle' }} text-success"></i>
                        @break
                    @case('error')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fas fa-times-circle' }} text-danger"></i>
                        @break
                    @case('warning')
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fas fa-exclamation-triangle' }} text-warning"></i>
                        @break
                    @default
                        <i class="alert-ico {{ !empty($alert->icon) ? $alert->icon : 'fas fa-info-circle' }} text-info"></i>
                @endswitch
                <div>
                    <div class="alert-title">{{ $alert->text }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
