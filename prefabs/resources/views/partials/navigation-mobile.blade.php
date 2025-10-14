<div class="layout-nav-mobile">
    <ul class="app-nav nav">
        <li class="nav-item is-active nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-home"></i>
                <apan class="nav-link-content">{{ __('Home') }}</apan>
            </a>
        </li>
        <li class="nav-item nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-users"></i>
                <span class="nav-link-content">{{ __('Link') }}</span>
            </a>
        </li>
        <li class="nav-item nav-item-mobile">
            <a class="nav-link" href="#">
                <i class="nav-link-ico fas fa-cog"></i>
                <span class="nav-link-content">{{ __('Test') }}</span>
            </a>
        </li>

        <li class="mt-4 text-muted"><small>{{ __('System') }}</small></li>
        <li class="nav-item {{ request()->routeIs('system.log.index') ? 'is-active' : '' }}">
            <a class="nav-link" href="{{ route('system.log.index') }}">
                <i class="nav-link-ico fas fa-bug"></i>
                {{ __('Log') }}
            </a>
        </li>
         <li class="nav-item {{ request()->routeIs('system.audit.index') ? 'is-active' : '' }}">
            <a class="nav-link" href="{{ route('system.audit.index') }}">
                <i class="nav-link-ico fas fa-eye"></i>
                {{ __('Audit') }}
            </a>
        </li>
    </ul>
</div>
