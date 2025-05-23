<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>Welcolm Back !</h1>
            <a class="btn btn-primary" href="{{ url('home') }}"><i class="fa fa-plus me-2"></i> Page Action</a>
        </div>
    </div>
    <div class="container-xl">

        <h1>head 1</h1>
        <h2>head 2</h2>
        <h3>head 3</h3>
        <h4>head 4</h4>
        <h5>head 5</h5>
        <h6>head 6</h6>

        <div class="my-4">
            <h4>Tabs</h4>
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                </li>
            </ul>
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Pills</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                </li>
            </ul>
            <ul class="nav nav-switch mb-4">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Switch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                </li>
            </ul>
        </div>

        <div class="my-4 d-flex">
            <x-avatar :user="auth()->user()" size="lg"/>
            <x-avatar name="Test test test" />
            <x-avatar name="S M" size="sm"/>
            <x-avatar name="X S" size="xs"/>
            <x-avatar name="Test long very" />
            <x-avatar name="System" color="0"/>
        </div>

        @php
            $themes = [
                'primary',
                'secondary',
                'success',
                'info',
                'warning',
                'danger',
                'light',
                'dark',
            ]
        @endphp

        <div class="my-4">
            <h4>Buttons</h4>
            @foreach ($themes as $theme)
                        <button type="button" class="btn btn-{{$theme}}">{{ucfirst($theme)}}</button>
                    @endforeach
            <button type="button" class="btn">Button</button>
            <button type="button" class="btn btn-link">Link</button>
            <br>
            <br>

            <div data-bs-theme="dark">
                <div class="bg-body p-4">
                    @foreach ($themes as $theme)
                        <button type="button" class="btn btn-{{$theme}}">{{ucfirst($theme)}}</button>
                    @endforeach
                    <button type="button" class="btn">Button</button>
                </div>
            </div>

            <br>
            <br>
            <button type="button" class="btn btn-light btn-sq"><i class="fas fa-angle-double-up"></i></button>
            <button type="button" class="btn btn-sm btn-light"><i class="fas fa-angle-down me-2"></i><small>System</small></button>
            <button type="button" class="btn btn-sq">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                  </svg>
            </button>
            <br>
            <div class="d-flex gap-2 my-2">
                <input type="text" class="form-control w-auto">
                <button type="button" class="btn btn-light">Light</button>
                <button type="button" class="btn border">Light</button>
            </div>
            <button type="button" class="btn btn-sm border">Small</button>
            <button type="button" class="btn btn-lg border">Large</button>
        </div>

        <div class="my-4">
            <h4>Badges</h4>
            @foreach ($themes as $theme)
                <x-badge color="{{$theme}}" icon="far fa-circle">{{ucfirst($theme)}}</x-badge>
            @endforeach
            <br>
            <br>

            @foreach ($themes as $theme)
                <x-badge color="{{$theme}}" variant="subtle" icon="far fa-circle">{{ucfirst($theme)}}</x-badge>
            @endforeach
            <br>
            <br>

            <div data-bs-theme="dark">
                <div class="bg-body p-4">
                    @foreach ($themes as $theme)
                        <x-badge color="{{$theme}}" icon="far fa-circle">{{ucfirst($theme)}}</x-badge>
                    @endforeach
                    <br>
                    <br>

                    @foreach ($themes as $theme)
                        <x-badge color="{{$theme}}" variant="subtle" icon="far fa-circle">{{ucfirst($theme)}}</x-badge>
                    @endforeach
                </div>
            </div>
            <br>

            <x-badge color="primary" variant="subtle" icon="far fa-circle" size="sm">Small</x-badge>
            <x-badge color="primary" variant="subtle" icon="far fa-circle" size="md">Normal</x-badge>
            <x-badge color="primary" variant="subtle" icon="far fa-circle" size="lg">Large</x-badge>
            <br>
            <x-badge color="primary" variant="subtle" size="sm">Small</x-badge>
            <x-badge color="primary" variant="subtle" size="md">Normal</x-badge>
            <x-badge color="primary" variant="subtle" size="lg">Large</x-badge>

        </div>

        <div class="my-4">
            <h4>Dropdowns</h4>

            @php
                $priorities = [
                    ['danger', 'fas fa-skull-crossbones', 'Critical'],
                    ['warning', 'fas fa-angle-double-up', 'High'],
                    ['info', 'fas fa-equals', 'Normal'],
                    ['light', 'fas fa-angle-double-down', 'Low'],
                ];
            @endphp

            <div class="row g-3">
                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <x-badge color="{{$item[0]}}" variant="subtle" icon="{{$item[1]}}">{{$item[2]}}</x-badge>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <x-badge color="{{$item[0]}}" variant="subtle" icon="{{$item[1]}}" size="lg">{{$item[2]}}</x-badge>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <i class="dropdown-ico {{$item[1]}} text-{{$item[0]}}"></i>
                                {{$item[2]}}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <i class="dropdown-ico {{$item[1]}} text-{{$item[0]}}-emphasis bg-{{$item[0]}}-subtle"></i>
                                {{$item[2]}}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <i class="dropdown-ico {{$item[1]}} text-{{$item[0]}} bg-{{$item[0]}}-subtle"></i>
                                {{$item[2]}}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <i class="dropdown-ico {{$item[1]}} text-{{$item[0]}} bg-{{$item[0]}}-subtle border border-{{$item[0]}}-subtle small"></i>
                                {{$item[2]}}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-2">
                    <div class="d-block dropdown-menu position-static">
                        @foreach ($priorities as $item)
                            <div class="dropdown-item">
                                <i class="dropdown-ico {{$item[1]}} text-bg-{{$item[0]}}"></i>
                                {{$item[2]}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>

        <div class="my-4">
            <h4>Breadcrumbs</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
            </nav>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Library</li>
                </ol>
            </nav>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Library</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data</li>
                </ol>
            </nav>
        </div>

        <div class="my-4">
            <h4>Alerts</h4>
            <div class="alert alert-primary" role="alert">
                A simple primary alert—check it out!
            </div>
            <div class="alert alert-secondary" role="alert">
                A simple secondary alert—check it out!
            </div>
            <div class="alert alert-success" role="alert">
                A simple success alert—check it out!
            </div>
            <div class="alert alert-danger" role="alert">
                A simple danger alert—check it out!
            </div>
            <div class="alert alert-warning" role="alert">
                A simple warning alert—check it out!
            </div>
            <div class="alert alert-info" role="alert">
                A simple info alert—check it out!
            </div>
            <div class="alert alert-light" role="alert">
                A simple light alert—check it out!
            </div>
            <div class="alert alert-dark" role="alert">
                A simple dark alert—check it out!
            </div>
        </div>

        <div class="my-4">
            <h4>Pagination</h4>
            <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item active" aria-current="page">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="my-4">
            <h4>Colors</h4>
            <div class="text-bg-primary p-3">Primary with contrasting color</div>
            <div class="text-bg-secondary p-3">Secondary with contrasting color</div>
            <div class="text-bg-success p-3">Success with contrasting color</div>
            <div class="text-bg-danger p-3">Danger with contrasting color</div>
            <div class="text-bg-warning p-3">Warning with contrasting color</div>
            <div class="text-bg-info p-3">Info with contrasting color</div>
            <div class="text-bg-light p-3">Light with contrasting color</div>
            <div class="text-bg-dark p-3">Dark with contrasting color</div>
        </div>
    </div>
</x-layout-app>
