<div class="layout-nav {{ Cookie::get('layout-nav') }}" id="layout-nav">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="dropdown">
                <div class="d-flex align-items-center">
                    <div class="app-nav-header flex-grow-1" data-bs-toggle="dropdown">
                        <div class="app-nav-logo random-bg-2">
                            {{-- <img src="{{ asset('storage/images/logo.png') }}" width="32px" height="32px"> --}}
                            DT
                        </div>
                        <div class="app-nav-header-content nav-collapsed-hide">
                            <div class="fw-semibold">{{ config('app.name', 'Laravel') }}</div>
                            <small class="text-body-secondary">steelants.cz</small>
                        </div>
                        <div class="ms-auto nav-collapsed-hide">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-expand" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M3.646 9.146a.5.5 0 0 1 .708 0L8 12.793l3.646-3.647a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708m0-2.292a.5.5 0 0 0 .708 0L8 3.207l3.646 3.647a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 0 0 0 .708"/>
                            </svg>
                        </div>
                    </div>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">
                            <div class="dropdown-img random-bg-2">
                                SA
                            </div>
                            {{-- <div class="dropdown-img">
                                <img src="{{ asset('storage/images/logo.png') }}" width="2rem" height="2rem">
                            </div> --}}
                            <div class="lh-1">
                                <div class="fw-semibold">Steelants</div>
                                <small class="text-body-secondary">1 member</small>
                            </div>
                        </a>
                        <a class="dropdown-item" href="#">
                            {{-- <div class="dropdown-img random-bg-3">
                                A
                            </div> --}}
                            <div class="dropdown-img">
                                <img src="{{ asset('storage/images/logo.png') }}" width="2rem" height="2rem">
                            </div>
                            <div class="lh-1">
                                <div class="fw-semibold">Anthill</div>
                                <small class="text-body-secondary">42 members</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-content">
            <ul class="app-nav nav flex-column">
                @foreach ($mainMenuItems as $item)
                    <li class="nav-item {{ ($item->isActive() || $item->isUse()) ? 'is-active' : '' }}">
                        <a class="nav-link" href="{{ route($item->route) }}">
                            <i class="nav-link-ico {{ $item->icon }}"></i>
                            <div class="nav-link-title">{{ __($item->title) }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if($systemMenuItems)
                {{-- MAIN NAVIGATION ALL --}}
                <div>
                    <div class="text-body-tertiary nav-collapsed-hide d-flex align-items-center small nav-title-toggle py-2 {{/*getToggleState('nav-system') == 'open' ? '' : 'collapsed'*/ ''}}" data-bs-toggle="collapse" data-bs-target="#nav-system" onclick="toggleSystemNav()">
                        <i class="fas fa-angle-down collapse-icon"></i>
                        <span class="fw-medium">{{ __('boilerplate::ui.system') }}</span>
                    </div>
                    <div class="remember collapse {{/*getToggleState('nav-system') == 'open' ? 'show' : ''*/ 'show'}}" id="nav-system">
                        <ul class="app-nav nav flex-column">
                            @foreach ($systemMenuItems as $item)
                                <li class="nav-item {{ $item->isActive() ? 'is-active' : '' }}">
                                    <a class="nav-link" href="{{ route($item->route) }}">
                                        <i class="nav-link-ico {{ $item->icon }}"></i>
                                        <div class="nav-link-title">{{ __($item->title) }}</div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                {{-- MAIN NAVIGATION SYSTEM --}}
            @endif
        </div>

        <div class="sidebar-footer">
            <ul class="app-nav nav flex-column mb-1 hide-mobile">
                <li class="nav-item">
                    <div type="button" class="nav-link" onclick="toggleLayoutNav()">
                        <i class="nav-link-ico fas fa-chevron-left nav-collapsed-hide"></i>
                        <i class="nav-link-ico fas fa-chevron-right nav-collapsed-show"></i>
                        <div class="nav-link-title">{{ __('Toggle menu') }}</div>
                    </div>
                </li>
            </ul>

            <div class="dropup">
                <a class="app-nav-user" data-bs-toggle="dropdown">
                    <x-avatar class="app-nav-profile" :user="auth()->user()"/>

                    <div class="app-nav-header-content nav-collapsed-hide">
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <small class="text-body-secondary">{{ Auth::user()->email }}</small>
                    </div>
                    <div class="ms-auto nav-collapsed-hide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                        </svg>
                    </div>
                </a>

                <div class="dropdown-menu">
                    <div class="dropdown-header d-flex align-items-center">
                        <div class="fw-semibold me-auto">{{ Auth::user()->name }}</div>
                        <small class="text-body-tertiary">v1.23</small>
                    </div>

                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="dropdown-ico  fas fa-user"></i>
                        {{ __('boilerplate::ui.profile') }}
                    </a>

                    <label class="dropdown-item">
                        <i class="dropdown-ico fas fa-moon"></i>
                        <div class="me-auto">{{ __('boilerplate::ui.dark_mode') }}</div>
                        <div class="form-switch lh-1">
                            <input {{ Cookie::get('theme') == 'dark' ? 'checked' : '' }} class="form-check-input me-0" id="datk-theme" onchange="toggleDatkTheme()" type="checkbox">
                        </div>
                    </label>

                    <a class="dropdown-item" href="{{ route('profile.api') }}">
                        <i class="dropdown-ico  fas fa-server"></i>
                        {{ __('boilerplate::ui.api_tokens') }}
                    </a>

                    <hr class="dropdown-divider">

                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="dropdown-ico  fas fa-sign-out-alt text-danger"></i>
                        {{ __('boilerplate::ui.logout') }}
                    </a>

                    <form action="{{ route('logout') }}" class="d-none" id="logout-form" method="POST">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
