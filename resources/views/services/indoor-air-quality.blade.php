{{-- resources/views/services/indoor-air-quality.blade.php --}}
@extends('app')

@section('title', 'Indoor Air Quality Solutions Nairobi | Air Purification & Filtration | ' . config('app.name'))

@section('meta_description', "Improve your indoor air quality with {{ config('app.name') }}. Air purifiers, humidifiers,
    ventilation systems, and UV sanitization. Breathe healthier air in your home or office.")

@section('meta_keywords', 'indoor air quality solutions, air purifier Nairobi, HVAC air filtration, allergen reduction,
    air quality testing near me, best IAQ company, humidifier installation Nairobi')

@section('content')
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 700px;">
                <h1 class="display-6 mb-3">Indoor Air Quality Solutions Nairobi</h1>
                <p class="lead mb-5">Breathe healthier air in your home or office with professional IAQ solutions from
                    <strong>{{ config('app.name') }}</strong> — your trusted <strong>HVAC experts in Nairobi</strong>.</p>
            </div>

            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-info bg-opacity-10 p-4 rounded mb-4">
                        <h4 class="mb-3"><i class="fa fa-info-circle text-info me-2"></i> Did You Know?</h4>
                        <p class="mb-0">Indoor air can be up to <strong>5 times more polluted</strong> than outdoor air.
                            Common pollutants include dust, mold, pet dander, VOCs, and bacteria that can affect your
                            family's health.</p>
                    </div>

                    <h4 class="mb-3">Our IAQ Services Include:</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-tint text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-0">Humidifiers & Dehumidifiers</h6>
                                    <small>Maintain optimal moisture levels</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-filter text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-0">HEPA Filtration Systems</h6>
                                    <small>Remove 99.97% of airborne particles</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-sun-o text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-0">UV Germicidal Lights</h6>
                                    <small>Kill bacteria, viruses, and mold</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-leaf text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-0">Ventilation Systems</h6>
                                    <small>Fresh air exchange solutions</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-4 rounded mt-3">
                        <h5>Benefits of Clean Indoor Air</h5>
                        <div class="row">
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Reduced allergies</li>
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Better sleep quality</li>
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Fewer respiratory issues</li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Eliminated odors</li>
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Protected HVAC system</li>
                                    <li><i class="fa fa-check-circle text-success me-2"></i> Improved productivity</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="position-relative rounded overflow-hidden mb-4">
                        <img class="img-fluid w-100" src="{{ asset('img/service-5.jpg') }}"
                            alt="Indoor air quality solutions by HVAC experts in Nairobi - {{ config('app.name') }}"
                            style="object-fit: cover; height: 300px; width: 100%;">
                    </div>

                    <div class="bg-primary text-white p-4 rounded">
                        <h4 class="text-white mb-3">Schedule Free IAQ Consultation</h4>
                        <p>Our <strong>HVAC experts</strong> will assess your indoor air quality and recommend the best
                            solution for your home or office.</p>
                        <h4 class="text-white"><i class="fa fa-phone-alt me-2"></i> +254 700 123 456</h4>
                        <a href="{{ url('/contact') }}" wire:navigate class="btn btn-light mt-3 w-100">Book Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
