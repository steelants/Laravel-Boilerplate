<x-layout>
    <div class="d-flex align-items-center p-4">
        <h5 class="mb-0">steelants/laravel-boilerplate</h5>
        <nav class="d-flex gap-2 ms-auto">
            @auth
                @if (Route::has('login'))
                    <a href="{{ url('/home') }}" class="btn">
                        Home
                    </a>
                @endif
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn text-nowrap">
                        Log in
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn">
                        Register
                    </a>
                @endif
            @endauth
            <label class="dropdown-item ms-2">
                <i class="dropdown-ico fas fa-moon"></i>
                <div class="form-switch lh-1">
                    <input class="form-check-input me-0" id="datk-theme" onchange="toggleDatkTheme()" type="checkbox">
                </div>
            </label>
        </nav>
    </div>

    <div class="container-xl py-8">
        <div class="text-center">
            <img class="mb-4" src="{{ asset('storage/images/logo.png') }}">
            <h1>steelants/laravel-boilerplate</h1>
            <a href="https://github.com/steelants/Laravel-Boilerplate">https://github.com/steelants/Laravel-Boilerplate</a>
        </div>

        <div class="mt-8 text-center">
            <h4>Other packages</h4>
            <div class="d-flex flex-wrap gap-4 align-items-center justify-content-center">
                <a href="https://github.com/steelants/laravel-auth" target="_blank" class="btn border">laravel-auth</a>
                <a href="https://github.com/steelants/Livewire-DataTable" target="_blank" class="btn border">datatable</a>
                <a href="https://github.com/steelants/Laravel-Form" target="_blank" class="btn border">form</a>
                <a href="https://github.com/steelants/Livewire-Modal" target="_blank" class="btn border">modal</a>
                <a href="https://github.com/steelants/Laravel-Tenant" target="_blank" class="btn border">laravel-tenant</a>
                <a href="https://github.com/steelants/Livewire-Form" target="_blank" class="btn border">livewire-form</a>
            </div>
        </div>

        <div class="text-center mt-8">
            Created by <br>
            <a href="https://steelants.cz" target="_blank" class="d-inline-block">
                <img class="mb-4 dark-invert" src="{{ asset('storage/images/steelants.png') }}" width="130px">
            </a>
        </div>

        <div class="card card-body p-0" style="height: 500px">
            <div class="layout">
                <div class="layout-nav " id="layout-nav">
                    <div class="sidebar">
                        <div class="sidebar-header">
                            <div class="dropdown">
                                <div class="d-flex align-items-center">
                                    <div class="app-nav-header flex-grow-1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="app-nav-logo random-bg-2">LB</div>
                                        <div class="app-nav-header-content nav-collapsed-hide">
                                            <div class="fw-semibold">Laravel Boilerplate</div>
                                            <small class="text-body-secondary">steelants.cz</small>
                                        </div>
                                        <div class="ms-auto nav-collapsed-hide">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-chevron-expand" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M3.646 9.146a.5.5 0 0 1 .708 0L8 12.793l3.646-3.647a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708m0-2.292a.5.5 0 0 0 .708 0L8 3.207l3.646 3.647a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 0 0 0 .708">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="#">
                                            <div class="dropdown-img random-bg-2">
                                                SA
                                            </div>

                                            <div class="lh-1">
                                                <div class="fw-semibold">Steelants</div>
                                                <small class="text-body-secondary">1 member</small>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="#">

                                            <div class="dropdown-img">
                                                <img src="#storage/images/logo.png" width="2rem" height="2rem">
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
                                <li class="nav-item is-active">
                                    <a class="nav-link" href="#home">
                                        <i class="nav-link-ico  fas fa-home"></i>
                                        <div class="nav-link-title">Home</div>
                                    </a>
                                </li>
                                @include('demo-nav')
                            </ul>
                        </div>

                        <div class="sidebar-footer">
                            <ul class="app-nav nav flex-column mb-1 hide-mobile">
                                <li class="nav-item">
                                    <div type="button" class="nav-link" onclick="toggleLayoutNav()">
                                        <i class="nav-link-ico fas fa-chevron-left nav-collapsed-hide"></i>
                                        <i class="nav-link-ico fas fa-chevron-right nav-collapsed-show"></i>
                                        <div class="nav-link-title">Toggle menu</div>
                                    </div>
                                </li>
                            </ul>

                            <div class="dropup">
                                <a class="app-nav-user" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="avatar avatar-md random-bg-5 app-nav-profile" title=" Test User">
                                        TU
                                    </div>

                                    <div class="app-nav-header-content nav-collapsed-hide">
                                        <div class="fw-semibold">Test User</div>
                                        <small class="text-body-secondary">test@example.com</small>
                                    </div>
                                    <div class="ms-auto nav-collapsed-hide">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                            <path
                                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0">
                                            </path>
                                        </svg>
                                    </div>
                                </a>

                                <div class="dropdown-menu" style="">
                                    <div class="dropdown-header d-flex align-items-center">
                                        <div class="fw-semibold me-auto">Test User</div>
                                        <small class="text-body-tertiary">v1.23</small>
                                    </div>

                                    <a class="dropdown-item" href="#profile">
                                        <i class="dropdown-ico  fas fa-user"></i>
                                        Profile
                                    </a>

                                    <label class="dropdown-item">
                                        <i class="dropdown-ico fas fa-moon"></i>
                                        <div class="me-auto">Dark Mode</div>
                                        <div class="form-switch lh-1">
                                            <input class="form-check-input me-0" id="datk-theme" onchange="toggleDatkTheme()"
                                                type="checkbox">
                                        </div>
                                    </label>

                                    <a class="dropdown-item" href="#profile/api">
                                        <i class="dropdown-ico  fas fa-server"></i>
                                        Api Tokens
                                    </a>

                                    <hr class="dropdown-divider">

                                    <a class="dropdown-item" href="#logout"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="dropdown-ico  fas fa-sign-out-alt text-danger"></i>
                                        Logout
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layout-content">
                    <div class="content">
                        <div class="container-xl">
                            <div class="page-header">
                                <h1>Welcolm Back !</h1>
                                <a class="btn btn-primary" href="#home"><i class="fa fa-plus me-2"></i> Page Action</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
