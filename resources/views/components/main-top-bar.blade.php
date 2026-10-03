    <div class="container-fluid bg-dark text-white-50 py-2 px-0 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="fa fa-phone-alt me-2"></small>
                    <small>{{ config('site.phone') }}</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="far fa-envelope-open me-2"></small>
                    <small>{{ config('site.email') }}</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <ol class="breadcrumb justify-content-end mb-0 d-flex flex-row align-items-center gap-2">
                    @if (Auth::user())
                        <li class="breadcrumb-item"><a class="text-white-50 small"
                                href="{{ route('dashboard') }}">Dashboard</a></li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-danger" type="submit">Logout</button>
                        </form>
                    @else
                        <li class="breadcrumb-item"><a class="text-white-50 small" href="{{ route('login') }}">Login</a>
                        </li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
