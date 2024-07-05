<div class="layout-nav">
    <div class="dropdown mb-2">
        <a class="app-nav-header mb-2" data-bs-toggle="dropdown">
            <div class="app-nav-logo">
                <img src="{{ asset('storage/images/logo.png') }}" width="32px" height="32px">
            </div>
            {{-- <div class="app-nav-logo random-bg-2">
                SA
            </div> --}}
            <div class="app-nav-header-content">
                <div class="fw-semibold">{{ config('app.name', 'Laravel') }}</div>
                {{-- <div class="fw-semibold">Steelants</div> --}}
                {{-- <small class="text-body-secondary">steelants.cz</small> --}}
            </div>
            <div class="ms-auto">
                <svg class="bi bi-chevron-expand" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.646 9.146a.5.5 0 0 1 .708 0L8 12.793l3.646-3.647a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708m0-2.292a.5.5 0 0 0 .708 0L8 3.207l3.646 3.647a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 0 0 0 .708" />
                </svg>
            </div>
        </a>

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

    <ul class="app-nav nav flex-column">
        @foreach ($mainMenuItems as $item)
            <li class="nav-item {{ ($item->isActive() || $item->isUse()) ? 'is-active' : '' }}">
                <a class="nav-link" href="{{ route($item->route) }}">
                    <i class="nav-link-ico {{ $item->icon }}"></i>
                    {{ __($item->title) }}
                </a>
            </li>
        @endforeach
        {{-- MAIN NAVIGATION ALL --}}
        @auth
            <li class="mt-4 text-body-secondary"><small>{{ __('boilerplate::ui.system') }}</small></li>
            @foreach ($systemMenuItems as $item)
                <li class="nav-item {{ ($item->isActive() || $item->isUse()) ? 'is-active' : '' }}">
                    <a class="nav-link" href="{{ route($item->route, $item->parameters) }}">
                        <i class="nav-link-ico {{ $item->icon }}"></i>
                        {{ __($item->title) }}
                    </a>
                </li>
            @endforeach
            {{-- MAIN NAVIGATION SYSTEM --}}
        @endauth
    </ul>

    <div class="mt-auto">
        <div class="dropup">
            <a class="app-nav-user" data-bs-toggle="dropdown">
                <div class="app-nav-profile random-bg-1">
                    PS
                </div>
                <div class="app-nav-header-content">
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <small class="text-body-secondary">{{ Auth::user()->email }}</small>
                </div>
                <div class="ms-auto">
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
                    <div class="form-switch ms-4">
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
