{{-- resources/views/services/ac-installation.blade.php --}}
@extends('app')

@section('title', 'Professional AC Installation Services in Nairobi Kenya | ' . config('app.name'))

@section('meta_description',
    'Looking for AC installation near me? {{ config("app.name") }} offers expert air
    conditioner installation by certified HVAC technicians in Nairobi. Same-day service, competitive pricing, and warranty
    included.')

@section('meta_keywords',
    'AC installation near me, air conditioner installation Nairobi, HVAC installation Kenya,
    central AC installation, split AC installation, commercial AC installation, residential AC installation, best HVAC
    company near me')

@section('content')
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">AC Installation</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 mb-4">Professional AC Installation Services in Nairobi |
                        <strong>{{ config('app.name') }}</strong>
                    </h1>
                    <p class="mb-4">Searching for <strong>AC installation near me</strong>? Look no further.
                        <strong>{{ config('app.name') }}</strong> is your trusted <strong>HVAC company in Nairobi</strong>
                        offering expert air conditioner installation for residential and commercial properties. Our
                        certified <strong>HVAC technicians</strong> ensure your new cooling system is installed correctly
                        for optimal performance and energy efficiency.
                    </p>

                    <div class="bg-light p-4 rounded mb-4">
                        <h4 class="mb-3">Why Nairobi Homeowners Choose Us for AC Installation</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> <strong>Same-day
                                            service</strong> across Nairobi</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> <strong>Free
                                            consultation</strong> & load calculation</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>
                                        <strong>Certified HVAC experts</strong> with 5+ years experience
                                    </li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>
                                        <strong>Energy-efficient</strong> system recommendations
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>
                                        <strong>Competitive pricing</strong> & flexible payment plans
                                    </li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> <strong>1-year
                                            warranty</strong> on all installations</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> <strong>24/7
                                            emergency support</strong></li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> <strong>Free
                                            disposal</strong> of old AC units</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h4 class="mb-3">Types of AC Systems We Install</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <i class="fa fa-snowflake-o text-primary fa-2x mb-2"></i>
                                <h6>Split AC Systems</h6>
                                <p class="small mb-0">Perfect for homes and offices. Quiet operation with excellent cooling
                                    efficiency.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <i class="fa fa-building-o text-primary fa-2x mb-2"></i>
                                <h6>Central AC Systems</h6>
                                <p class="small mb-0">Ideal for large homes and commercial spaces. Whole-building cooling
                                    solution.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <i class="fa fa-window-maximize text-primary fa-2x mb-2"></i>
                                <h6>Window AC Units</h6>
                                <p class="small mb-0">Affordable and efficient for single rooms and small apartments.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <i class="fa fa-industry text-primary fa-2x mb-2"></i>
                                <h6>Commercial HVAC Systems</h6>
                                <p class="small mb-0">Custom solutions for offices, restaurants, hotels, and retail spaces.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary">
                        <i class="fa fa-phone-alt me-2"></i> <strong>Need AC installation today?</strong> Call us now at
                        <strong>{{ config('site.phone') }}</strong> for a free quote.
                    </div>
                </div>

                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="position-relative rounded overflow-hidden mb-4">
                        <img class="img-fluid w-100" src="{{ asset('img/air-conditioning-repair-nairobi-kenya.jpeg') }}"
                            alt="AC installation by HVAC experts in Nairobi - {{ config('app.name') }}"
                            style="object-fit: cover; height: 300px; width: 100%;">
                    </div>

                    <div class="bg-light text-dark text-dark p-4 rounded mb-4">
                        <h4 class="text-dark mb-3">Get Your Free AC Installation Quote</h4>
                        <livewire:contact-form />
                    </div>

                    <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded">
                        <div>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <p class="mb-0 mt-1">"Best AC installation service in Nairobi! Professional and fast."</p>
                            <small>- James M., Westlands</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schema Markup for Local Business -->
    <script type="application/ld+json">
    @verbatim
        
    
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "AC Installation Services",
    "provider": {
        "@type": "LocalBusiness",
        "name": "{!! config('app.name') !!}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nairobi",
            "addressCountry": "Kenya"
        }
    },
    "areaServed": "Nairobi, Kenya",
    "description": "Professional AC installation services by certified HVAC technicians in Nairobi. Same-day service, competitive pricing.",
    "offers": {
        "@type": "Offer",
        "priceCurrency": "KES",
        "availability": "https://schema.org/InStock"
    }
}
@endverbatim
</script>

@endsection
