{{-- resources/views/components/hero-carousel.blade.php --}}
<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            {{-- Slide 1: AC Installation --}}
            <div class="carousel-item active">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/air-conditioning-repair-nairobi-kenya.jpeg') }}"
                        alt="{{ config('app.name') }} professional AC installation services"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Expert AC Installation by
                                    <strong>{{ config('app.name') }}</strong></h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Upgrade to a
                                    high-efficiency cooling system today. Our certified technicians ensure perfect
                                    sizing, professional installation, and maximum energy savings — backed by our
                                    satisfaction guarantee.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2: Cooling Services --}}
            <div class="carousel-item">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/air-conditioning-repair-nairobi.jpeg') }}"
                        alt="{{ config('app.name') }} emergency cooling system repair"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Fast & Reliable Cooling
                                    Services</h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Same-day AC repair,
                                    routine maintenance, and system diagnostics.
                                    <strong>{{ config('app.name') }}</strong> keeps you cool when temperatures rise —
                                    with upfront pricing and 24/7 emergency support.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3: Heating Services --}}
            <div class="carousel-item">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/carousel-1.jpg') }}"
                        alt="{{ config('app.name') }} furnace and heating system services"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Stay Warm All Winter With
                                    <strong>{{ config('app.name') }}</strong></h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Furnace installation,
                                    boiler repair, heat pump services, and emergency heating solutions. Our team ensures
                                    your home stays cozy and energy-efficient all season long.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 4: Maintenance & Repair --}}
            <div class="carousel-item">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/carousel-4.jpg') }}"
                        alt="{{ config('app.name') }} HVAC maintenance and repair"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Preventative Maintenance
                                    Plans</h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Protect your investment
                                    with our annual tune-ups. <strong>{{ config('app.name') }}</strong> extends
                                    equipment life, improves efficiency, and catches problems before they become
                                    emergencies.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 5: Indoor Air Quality --}}
            <div class="carousel-item">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/Cold-room-condensing-unit.jpg') }}"
                        alt="{{ config('app.name') }} indoor air quality solutions"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Breathe Healthier Indoor Air
                                </h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Air purifiers,
                                    humidifiers, dehumidifiers, and UV sanitization.
                                    <strong>{{ config('app.name') }}</strong> helps reduce allergens, dust, and
                                    airborne contaminants for your family's health.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 6: Annual Inspections --}}
            <div class="carousel-item">
                <div class="carousel-image-wrapper" style="height: 600px; overflow: hidden;">
                    <img class="w-100 h-100" src="{{ asset('img/cooling-services-in-nairobi.jpeg') }}"
                        alt="{{ config('app.name') }} annual HVAC inspections"
                        style="object-fit: cover; object-position: center;">
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7 pt-5">
                                <h1 class="display-4 text-white mb-4 animated slideInDown">Comprehensive Annual
                                    Inspections</h1>
                                <p class="fs-5 text-body mb-4 pb-2 mx-sm-5 animated slideInDown">Stay ahead of costly
                                    breakdowns. <strong>{{ config('app.name') }}</strong> provides thorough system
                                    evaluations, safety checks, and performance optimization — giving you peace of mind
                                    year after year.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
