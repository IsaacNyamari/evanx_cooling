{{-- resources/views/services/heating-services.blade.php --}}
@extends('app')


@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Heating Services</li>
                    </ol>
                </nav>
                <h1 class="display-6 mb-4">Heating Services in Nairobi | Keep Your Home Warm This Winter</h1>
                <p class="mb-4"><strong>{{ config('app.name') }}</strong> is your trusted partner for <strong>heating services in Nairobi</strong>. Our <strong>certified HVAC technicians</strong> specialize in furnace installation, boiler repair, and complete heating system maintenance. Don't let the cold catch you unprepared — call the <strong>heating experts near me</strong> today.</p>

                <div class="bg-light bg-opacity-10 p-4 rounded mb-4">
                    <h4 class="mb-3"><i class="fa fa-fire text-danger me-2"></i> Pre-Winter Heating Checkup</h4>
                    <p class="mb-0">Schedule your <strong>furnace inspection</strong> before winter arrives. Our comprehensive heating system check ensures your home stays warm and energy-efficient all season long.</p>
                </div>

                <h4 class="mb-3">Our Heating Services Include:</h4>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Furnace Installation & Replacement</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Boiler Repair & Maintenance</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Heat Pump Services</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Radiant Heating Systems</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Thermostat Installation & Calibration</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Emergency Heating Repair</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Heating System Tune-Ups</li>
                            <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Ductwork Inspection & Sealing</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="position-relative rounded overflow-hidden mb-4">
                    <img class="img-fluid w-100" src="{{ asset('img/heating.jpg') }}" alt="Heating system repair by HVAC experts in Nairobi - {{ config('app.name') }}" style="object-fit: cover; height: 300px; width: 100%;">
                </div>

                <div class="bg-primary text-white p-4 rounded mb-4">
                    <h4 class="text-white mb-3">Why Choose {{ config('app.name') }} for Heating?</h4>
                    <ul class="list-unstyled text-white">
                        <li><i class="fa fa-check me-2"></i> Certified heating technicians</li>
                        <li><i class="fa fa-check me-2"></i> 24/7 emergency heating repair</li>
                        <li><i class="fa fa-check me-2"></i> Competitive, upfront pricing</li>
                        <li><i class="fa fa-check me-2"></i> Quality parts with warranty</li>
                        <li><i class="fa fa-check me-2"></i> Fast response across Nairobi</li>
                    </ul>
                </div>

                <div class="bg-light p-4 rounded text-center">
                    <h5>Need Heating Help Now?</h5>
                    <h3 class="text-primary mb-0">{{ config('site.phone') }}</h3>
                    <a href="{{ url('/contact') }}" wire:navigate class="btn btn-outline-primary mt-3">Request Service</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection