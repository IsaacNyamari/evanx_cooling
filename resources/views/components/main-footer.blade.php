{{-- resources/views/components/footer.blade.php --}}
<!-- Footer Start -->
<div class="container-fluid bg-dark footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6">
                <h1 class="text-white mb-4"><img class="img-fluid me-3 w-50" src="{{ asset('img/logos/icon.png') }}"
                        alt="{{ config('app.name') }} logo"></h1>
                <span>Kenya's trusted name in heating, cooling, and indoor air quality solutions. A commitment to
                    excellence since 2023, {{ config('app.name') }} delivers professional HVAC services backed by
                    certified technicians, honest pricing, and 24/7 emergency support. Your comfort is our
                    priority.</span>
            </div>
            <div class="col-md-6">
                <h5 class="text-light mb-4">Stay Updated</h5>
                <p>Subscribe to our newsletter for seasonal maintenance tips, exclusive discounts, and HVAC special
                    offers from {{ config('app.name') }}.</p>
                <div class="position-relative">
                    <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="email"
                        placeholder="Your email address">
                    <button type="button"
                        class="btn btn-primary py-2 px-3 position-absolute top-0 end-0 mt-2 me-2">Subscribe</button>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-light mb-4">Contact Us</h5>
                <p><i class="fa fa-map-marker-alt me-3"></i>Westlands, Nairobi, Kenya</p>
                <p><i class="fa fa-phone-alt me-3"></i>{{ config('site.phone') }}</p>
                <p><i class="fa fa-envelope me-3"></i>{{ config('site.email') }}</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-light mb-4">Our Services</h5>
                <a class="btn btn-link {{ request()->routeIs('ac.installation')?" active-link":'' }}" wire:navigate  href="{{ route('ac.installation') }}">AC Installation</a>
                <a class="btn btn-link {{ request()->routeIs('ac.cooling')?" active-link":'' }}" wire:navigate  href="{{ route('ac.cooling') }}">Cooling Services</a>
                <a class="btn btn-link {{ request()->routeIs('heating.services')?" active-link":'' }}" wire:navigate  href="{{ route('heating.services') }}">Heating Services</a>
                <a class="btn btn-link {{ request()->routeIs('maintenace.and.repair')?" active-link":'' }}" wire:navigate  href="{{ route('maintenace.and.repair') }}">Maintenance & Repair</a>
                <a class="btn btn-link {{ request()->routeIs('indoor.air.quality')?" active-link":'' }}" wire:navigate  href="{{ route('indoor.air.quality') }}">Indoor Air Quality</a>
                <a class="btn btn-link {{ request()->routeIs('annual.inspection')?" active-link":'' }}" wire:navigate  href="{{ route('annual.inspection') }}">Annual Inspections</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-light mb-4">Quick Links</h5>
                <a class="btn btn-link {{ request()->routeIs('about')?" active-link":'' }}" wire:navigate  href="{{ route('about') }}">About Us</a>
                <a class="btn btn-link {{ request()->routeIs('shop*')?" active-link":'' }}" wire:navigate  href="{{ route('shop') }}">Shop</a>
                <a class="btn btn-link {{ request()->routeIs('contact')?" active-link":'' }}" wire:navigate  href="{{ route('contact') }}">Contact Us</a>
                <a class="btn btn-link {{ request()->routeIs(['services','ac.installation','ac.cooling','maintenace.and.repair','indoor.air.quality','heating.services','annual.inspection'])?" active-link":'' }}" wire:navigate  href="{{ route('services') }}">Our Services</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-light mb-4">Follow Us</h5>
                <p class="mb-3">Connect with {{ config('app.name') }} on social media for updates, tips, and
                    promotions.</p>
                <div class="d-flex">
                    <a class="btn btn-square rounded-circle me-2" href="#" aria-label="Twitter"><i
                            class="fab fa-twitter"></i></a>
                    <a class="btn btn-square rounded-circle me-2" href="{{ config('site.facebook') }}" target="_blank"
                        aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-square rounded-circle" href="{{ config('site.youtube') }}" target="_blank"
                        aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; {{ date('Y') }} <a href="#">{{ config('app.name') }}</a>, All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Designed With <i class="fa fa-heart text-danger"></i> By <a href="#">Pro Codes
                        Technologies</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i
        class="bi bi-arrow-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('lib/wow/wow.min.js') }}"></script>
<script src="{{ asset('lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('js/main.js') }}"></script>
