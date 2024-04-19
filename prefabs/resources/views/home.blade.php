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
            <ul class="nav nav-pills">
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
        </div>

        <div class="my-4 d-flex">
            <div class="app-nav-profile random-bg-1">
                PS
            </div>
            <div class="app-nav-profile random-bg-2">
                PS
            </div>
            <div class="app-nav-profile random-bg-3">
                PS
            </div>
            <div class="app-nav-profile random-bg-4">
                PS
            </div>
            <div class="app-nav-profile random-bg-5">
                PS
            </div>
            <div class="app-nav-profile random-bg-6">
                PS
            </div>
        </div>

        <div class="my-4">
            <h4>Buttons</h4>
            <button type="button" class="btn btn-primary">Primary</button>
            <button type="button" class="btn btn-secondary">Secondary</button>
            <button type="button" class="btn btn-success">Success</button>
            <button type="button" class="btn btn-danger">Danger</button>
            <button type="button" class="btn btn-warning">Warning</button>
            <button type="button" class="btn btn-info">Info</button>
            <button type="button" class="btn btn-light">Light</button>
            <button type="button" class="btn btn-dark">Dark</button>
            <button type="button" class="btn">Button</button>
            <button type="button" class="btn btn-link">Link</button>
        </div>

        <div class="my-4">
            <h4>Badges</h4>
            <span class="badge text-bg-primary">Primary</span>
            <span class="badge text-bg-secondary">Secondary</span>
            <span class="badge text-bg-success">Success</span>
            <span class="badge text-bg-danger">Danger</span>
            <span class="badge text-bg-warning">Warning</span>
            <span class="badge text-bg-info">Info</span>
            <span class="badge text-bg-light">Light</span>
            <span class="badge text-bg-dark">Dark</span>
            <br>
            <span class="badge rounded-pill text-bg-primary">Primary</span>
            <span class="badge rounded-pill text-bg-secondary">Secondary</span>
            <span class="badge rounded-pill text-bg-success">Success</span>
            <span class="badge rounded-pill text-bg-danger">Danger</span>
            <span class="badge rounded-pill text-bg-warning">Warning</span>
            <span class="badge rounded-pill text-bg-info">Info</span>
            <span class="badge rounded-pill text-bg-light">Light</span>
            <span class="badge rounded-pill text-bg-dark">Dark</span>
            <br>
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
