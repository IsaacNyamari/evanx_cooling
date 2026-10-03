{{-- resources/views/services/annual-inspections.blade.php --}}
@extends('app')


@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Annual Inspections</li>
                    </ol>
                </nav>
                <h1 class="display-6 mb-4">Annual HVAC Inspections Nairobi | Protect Your Investment</h1>
                <p class="mb-4">Regular <strong>HVAC inspections</strong> are crucial for maintaining system efficiency and preventing costly breakdowns. <strong>{{ config('app.name') }}</strong> offers comprehensive <strong>annual inspection services</strong> for both residential and commercial HVAC systems across Nairobi. Our <strong>certified technicians</strong> perform thorough system evaluations to catch problems before they become emergencies.</p>

                <div class="bg-primary text-white p-4 rounded mb-4">
                    <h4 class="text-white mb-3">What's Included in Our Annual Inspection?</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled text-white">
                                <li><i class="fa fa-check me-2"></i> Thermostat calibration</li>
                                <li><i class="fa fa-check me-2"></i> Electrical connection check</li>
                                <li><i class="fa fa-check me-2"></i> Refrigerant level test</li>
                                <li><i class="fa fa-check me-2"></i> Air filter replacement</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled text-white">
                                <li><i class="fa fa-check me-2"></i> Ductwork inspection</li>
                                <li><i class="fa fa-check me-2"></i> Safety control testing</li>
                                <li><i class="fa fa-check me-2"></i> System efficiency report</li>
                                <li><i class="fa fa-check me-2"></i> Maintenance recommendations</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-light text-dark bg-opacity-10 p-3 rounded">
                    <i class="fa fa-chart-line text-success me-2"></i> <strong>Energy Savings Guarantee:</strong> Regular inspections can reduce energy costs by 15-30%. Schedule yours today!
                </div>
            </div>

            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="position-relative rounded overflow-hidden mb-4">
                    <img class="img-fluid w-100" src="{{ asset('img/service-6.jpg') }}" alt="Annual HVAC inspection by certified experts in Nairobi - {{ config('app.name') }}" style="object-fit: cover; height: 300px; width: 100%;">
                </div>

                <div class="bg-light p-4 rounded">
                    <h4 class="mb-3">Schedule Your Annual Inspection</h4>
                    <p>Don't wait for a breakdown. Book your <strong>HVAC inspection</strong> today and enjoy peace of mind.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        
                        <a href="{{ url('/contact') }}" class="btn btn-primary">Book Now</a>
                    </div>
                    <hr>
                    <p class="small text-muted mb-0"><i class="fa fa-shield-alt me-1"></i> All inspections include a detailed report and 30-day follow-up support.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="text-center p-3 border rounded">
                    <i class="fa fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5>Annual Checkups</h5>
                    <small>Extend equipment life</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                <div class="text-center p-3 border rounded">
                    <i class="fa fa-dollar-sign fa-3x text-primary mb-3"></i>
                    <h5>Lower Energy Bills</h5>
                    <small>Save up to 30%</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="text-center p-3 border rounded">
                    <i class="fa fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5>Prevent Breakdowns</h5>
                    <small>Catch issues early</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.4s">
                <div class="text-center p-3 border rounded">
                    <i class="fa fa-leaf fa-3x text-primary mb-3"></i>
                    <h5>Eco-Friendly</h5>
                    <small>Reduce carbon footprint</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/ld+json">
    @verbatim
        

{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "Annual HVAC Inspections",
    "provider": {
        "@type": "LocalBusiness",
        "name": "{{ config('app.name') }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nairobi",
            "addressCountry": "Kenya"
        }
    },
    "description": "Comprehensive annual HVAC inspections in Nairobi. Includes safety checks, efficiency testing, and maintenance recommendations.",
    "offers": {
        "@type": "Offer",
        "price": "3500",
        "priceCurrency": "KES"
    }
}    @endverbatim
</script>
@endsection