<div class="layout-nav">
    <ul class="app-nav nav">
        <li class="nav-item is-active nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-home"></i>
                <apan class="nav-link-content">Home</apan>
            </a>
        </li>
        <li class="nav-item nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-users"></i>
                <span class="nav-link-content">Link</span>
            </a>
        </li>
        <li class="nav-item nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-cog"></i>
                <span class="nav-link-content">Test</span>
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
    </ul>
</div>
