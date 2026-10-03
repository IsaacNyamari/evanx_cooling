{{-- resources/views/components/services-section.blade.php --}}
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 700px;">
            <h1 class="display-6 mb-3">Professional Heating & Cooling Services</h1>
            <p class="lead mb-5">Keep your home comfortable year-round with expert HVAC solutions from <strong>{{ config('app.name') }}</strong>. Our certified technicians deliver reliable installations, repairs, and maintenance you can trust.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <img class="img-fluid grayscale" style="height: 250px !important;" src="{{ asset('img/service-1.jpg') }}" alt="Professional AC installation by {{ config('app.name') }} technicians">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-01-light.png') }}" alt="AC Installation Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('ac.installation') }}" wire:navigate>AC Installation</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Expert installation of energy-efficient air conditioners. {{ config('app.name') }} ensures proper sizing and optimal performance for maximum energy savings and home comfort.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item">
                    <img class="img-fluid" style="height: 250px !important;" src="{{ asset('img/service-2.jpg') }}" alt="Cooling system repair and maintenance by {{ config('app.name') }}">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-02-light.png') }}" alt="Cooling Services Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('ac.cooling') }}" wire:navigate>Cooling Services</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Fast, reliable AC repairs and routine tune-ups. {{ config('app.name') }} keeps your cooling system running efficiently all summer long with 24/7 emergency service.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item">
                    <img class="img-fluid" style="height: 250px !important;" src="{{ asset('img/heating.jpg') }}" alt="Heating system installation and repair by {{ config('app.name') }}">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-03-light.png') }}" alt="Heating Services Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('heating.services') }}" wire:navigate>Heating Services</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Stay warm with furnace and boiler services from {{ config('app.name') }}. From installations to emergency repairs, we ensure your home stays cozy all winter.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <img class="img-fluid" style="height: 250px !important;" src="{{ asset('img/service-4.jpg') }}" alt="HVAC maintenance and repair services by {{ config('app.name') }}">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-04-light.png') }}" alt="Maintenance and Repair Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('maintenace.and.repair') }}" wire:navigate>Maintenance & Repair</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Protect your investment with regular maintenance from {{ config('app.name') }}. Our preventative care plans reduce breakdowns and extend equipment lifespan.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item">
                    <img class="img-fluid" style="height: 250px !important;" src="{{ asset('img/service-5.jpg') }}" alt="Indoor air quality solutions by {{ config('app.name') }}">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-05-light.png') }}" alt="Indoor Air Quality Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('indoor.air.quality') }}" wire:navigate>Indoor Air Quality</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Breathe healthier air with purification and humidity solutions from {{ config('app.name') }}. Perfect for allergy sufferers and families seeking cleaner indoor environments.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item">
                    <img class="img-fluid" style="height: 250px !important;" src="{{ asset('img/service-6.jpg') }}" alt="Annual HVAC inspections by {{ config('app.name') }}">
                    <div class="d-flex align-items-center bg-light">
                        <div class="service-icon flex-shrink-0 bg-primary">
                            <img class="img-fluid" src="{{ asset('img/icon/icon-06-light.png') }}" alt="Annual Inspections Icon">
                        </div>
                        <a class="h4 mx-4 mb-0" href="{{ route('annual.inspection') }}">Annual Inspections</a>
                    </div>
                    <div class="p-4">
                        <p class="mb-0">Schedule your yearly check-up with {{ config('app.name') }}. Our comprehensive inspections catch issues early and ensure peak system performance year after year.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>