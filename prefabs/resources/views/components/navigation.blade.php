<div class="layout-nav">
    <ul class="app-nav nav flex-column">
        @dump($mainMenu);
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
        {{-- MAIN NAVIGATION ALL --}}
        @auth
            <li class="mt-4 text-muted"><small>{{ __('boilerplate::ui.system') }}</small></li>
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
            <li class="nav-item {{ request()->routeIs('system.subscription.index') ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route('system.subscription.index') }}">
                    <i class="nav-link-ico fas fa-dollar-sign"></i>
                    {{ __('boilerplate::subscriptions.title') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('system.log.index') ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route('system.log.index') }}">
                    <i class="nav-link-ico fas fa-bug"></i>
                    {{ __('boilerplate::ui.log') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('system.jobs.index') ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route('system.jobs.index') }}">
                    <i class="nav-link-ico fas fa-business-time"></i>
                    {{ __('boilerplate::ui.jobs') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('system.cache.index') ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route('system.cache.index') }}">
                    <i class="nav-link-ico fas fa-box"></i>
                    {{ __('boilerplate::ui.cache') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('system.backup.index') ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route('system.backup.index') }}">
                    <i class="nav-link-ico fas fa-file-archive"></i>
                    {{ __('boilerplate::ui.backup') }}
                </a>
            </li>
            {{-- MAIN NAVIGATION SYSTEM --}}
        @endauth
    </ul>
</div>
