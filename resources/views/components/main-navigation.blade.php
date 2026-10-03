    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5">
        <a href="/" class="navbar-brand d-flex align-items-center">
            <h1 class="m-0"><img class="img-fluid me-3" src="{{ asset('img/logos/') }}" alt=""></h1>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto bg-light pe-4 py-3 py-lg-0">
                <a href="{{ route('home') }}" wire:navigate
                    class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" wire:navigate
                    class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>

                <a href="{{ route('services') }}" wire:navigate
                    class="nav-item nav-link {{ request()->routeIs(['services','ac.installation','ac.cooling','maintenace.and.repair','indoor.air.quality','heating.services','annual.inspection']) ? 'active' : '' }}">Services</a>
                <a href="{{ route('shop') }}" wire:navigate
                    class="nav-item nav-link {{ request()->routeIs('shop*') ? 'active' : '' }}">Shop</a>
                <a href="{{ route('contact') }}" wire:navigate
                    class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </div>
            <div class="h-100 d-lg-inline-flex align-items-center d-none">
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" class="fab fa-facebook-f"
                    href="{{ config('site.facebook') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" href="{{ config('site.youtube') }}"
                    target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </nav>
