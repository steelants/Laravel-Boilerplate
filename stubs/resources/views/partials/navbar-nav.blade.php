<ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" id="navbarDropdown" role="button" v-pre>
            {{ Auth::user()->name }}
        </a>

        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-end">
            <label class="align-items-center d-flex dropdown-item">
                <span class="flex-grow-1">
                    {{ __('boilerplate::ui.dark_mode') }}
                </span>
                <div class="form-switch ms-4">
                    <input {{ Cookie::get('theme') == 'dark' ? 'checked' : '' }} class="form-check-input me-0" id="datk-theme" onchange="toggleDatkTheme()" type="checkbox">
                </div>
            </label>

            <a class="dropdown-item" href="{{ route('profile.index') }}">
                {{ __('boilerplate::ui.profile') }}
            </a>

            <a class="dropdown-item" href="{{ route('profile.api') }}">
                {{ __('boilerplate::ui.api_tokens') }}
            </a>

            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                {{ __('boilerplate::ui.logout') }}
            </a>

            <form action="{{ route('logout') }}" class="d-none" id="logout-form" method="POST">
                @csrf
            </form>
        </div>
    </li>
</ul>