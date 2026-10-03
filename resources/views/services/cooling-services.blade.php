{{-- resources/views/services/cooling-services.blade.php --}}
@extends('app')


@section('content')
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cooling Services</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 mb-4">Cooling Services Near Me | Trusted AC Repair in Nairobi</h1>
                    <p class="mb-4">When searching for <strong>cooling services near me</strong>, choose
                        <strong>{{ config('app.name') }}</strong> — the leading <strong>HVAC company in Nairobi</strong>.
                        Our <strong>certified HVAC technicians</strong> provide fast, reliable AC repair and maintenance to
                        keep your home or business cool year-round.</p>

                    <div class="bg-light bg-opacity-10 p-4 rounded mb-4">
                        <h4 class="mb-3"><i class="fa fa-clock-o text-warning me-2"></i> Emergency Cooling Services
                            Available 24/7</h4>
                        <p class="mb-0">AC broken down in the middle of a hot Nairobi day? Call  <strong>{{ config('app.name') }}</strong> hotline. We respond within 2 hours anywhere in Nairobi metropolitan
                            area.</p>
                    </div>

                    <h4 class="mb-3">Our Cooling Services Include:</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="d-flex mb-3">
                                <i class="fa fa-wrench text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">AC Repair & Diagnostics</h6>
                                    <p class="small mb-0">Fast diagnosis and repair of all AC brands and models. We fix
                                        cooling issues, strange noises, leaks, and electrical problems.</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="fa fa-refresh text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">AC Maintenance & Tune-Ups</h6>
                                    <p class="small mb-0">Regular maintenance extends equipment life by up to 40%. Our
                                        <strong>AC tune-up service</strong> includes cleaning, filter replacement, and
                                        performance testing.</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="fa fa-gas-pump text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Gas Refill & Leak Repair</h6>
                                    <p class="small mb-0">Expert gas charging and leak detection. We use environmentally
                                        friendly refrigerants for all cooling systems.</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="fa fa-bolt text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Compressor & Fan Motor Replacement</h6>
                                    <p class="small mb-0">High-quality replacement parts with warranty. Our <strong>HVAC
                                            experts in Nairobi</strong> ensure your AC runs like new.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="position-relative rounded overflow-hidden mb-4">
                        <img class="img-fluid w-100" src="{{ asset('img/Cold-room-condensing-unit.jpg') }}"
                            alt="AC repair and cooling services by HVAC experts in Nairobi - {{ config('app.name') }}"
                            style="object-fit: cover; height: 300px; width: 100%;">
                    </div>

                    <div class="bg-light bg-opacity-10 p-4 rounded mb-4">
                        <h4 class="mb-3">Signs You Need AC Repair</h4>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fa fa-exclamation-triangle text-warning me-2"></i> Weak or no cool air flow</li>
                            <li><i class="fa fa-exclamation-triangle text-warning me-2"></i> Strange noises (grinding,
                                squealing)</li>
                            <li><i class="fa fa-exclamation-triangle text-warning me-2"></i> Water leaks or ice formation
                            </li>
                            <li><i class="fa fa-exclamation-triangle text-warning me-2"></i> Unpleasant odors when AC runs
                            </li>
                            <li><i class="fa fa-exclamation-triangle text-warning me-2"></i> Sudden increase in electricity
                                bills</li>
                        </ul>
                    </div>

                    <div class="bg-primary text-white p-4 rounded">
                        <h4 class="text-white mb-3">Need Cooling Services?</h4>
                        <p>Call  <strong>{{ config('app.name') }}</strong> today.</p>
                        <h3 class="text-white mb-3"><i class="fa fa-phone-alt me-2"></i> {{ config('site.phone') }}</h3>
                        <a href="{{ url('/contact') }}" class="btn btn-light w-100">Book a Service</a>
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
    "name": "Cooling Services",
    "provider": {
        "@type": "LocalBusiness",
        "name": "{{ config('app.name') }}",
        "telephone": "{{ config('site.phone') }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nairobi",
            "addressCountry": "Kenya"
        }
    },
    "areaServed": "Nairobi, Kenya",
    "serviceType": ["AC Repair", "AC Maintenance", "Cooling System Service"]
}@endverbatim
</script>
@endsection
