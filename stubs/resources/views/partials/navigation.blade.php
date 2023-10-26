<div class="layout-nav">
    <ul class="app-nav nav flex-column">
        <li class="nav-item is-active">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-home"></i>
                Link
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-users"></i>
                Link
            </a>
        </li>
        <li class="mt-4 text-muted"><small>{{ __('boilerplate::ui.system') }}</small></li>
        <li class="nav-item {{ request()->routeIs('system.log.index') ? 'is-active' : '' }}">
            <a class="nav-link" href="{{ route('system.log.index') }}">
                <i class="nav-link-ico fas fa-bug"></i>
                {{ __('boilerplate::ui.log') }}
            </a>
        </li>
         <li class="nav-item {{ request()->routeIs('system.audit.index') ? 'is-active' : '' }}">
            <a class="nav-link" href="{{ route('system.audit.index') }}">
                <i class="nav-link-ico fas fa-eye"></i>
                {{ __('boilerplate::ui.audit') }}
            </a>
        </li>
         <li class="nav-item {{ request()->routeIs('system.user.index') ? 'is-active' : '' }}">
            <a class="nav-link" href="{{ route('system.user.index') }}">
                <i class="nav-link-ico fas fa-users"></i>
                {{ __('boilerplate::ui.user') }}
            </a>
        </li>
    </ul>
</div>
