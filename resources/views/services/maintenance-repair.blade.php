{{-- resources/views/services/maintenance-repair.blade.php --}}
@extends('app')

@section('title', 'HVAC Maintenance & Repair Services Nairobi | AC & Heating Experts | ' . config('app.name'))

@section('meta_description', 'Professional HVAC maintenance and repair in Nairobi. {{ config("app.name") }} offers AC servicing, heating system repair, and preventative maintenance plans. Extend equipment life by 40%.')

@section('meta_keywords', 'HVAC maintenance near me, AC repair Nairobi, heating system repair, preventative maintenance HVAC, best HVAC maintenance company, affordable HVAC repair near me, HVAC experts Nairobi')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" wire:navigate>Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Maintenance & Repair</li>
                    </ol>
                </nav>
                <h1 class="display-6 mb-4">HVAC Maintenance & Repair Nairobi | Keep Your System Running Efficiently</h1>
                <p class="mb-4">Regular <strong>HVAC maintenance</strong> is essential for keeping your heating and cooling systems running efficiently. <strong>{{ config('app.name') }}</strong> offers comprehensive <strong>maintenance and repair services</strong> across Nairobi. Our <strong>certified HVAC technicians</strong> are available 24/7 for emergency repairs and scheduled tune-ups.</p>

                <div class="bg-success text-white p-3 rounded mb-4">
                    <i class="fa fa-gem me-2"></i> <strong>Pro Tip:</strong> Regular HVAC maintenance can reduce energy bills by up to 30% and extend equipment life by 40%. <a href="#" class="text-white fw-bold">View our maintenance plans →</a>
                </div>

                <div class="bg-light p-4 rounded">
                    <h4 class="mb-3">Emergency Repair Services</h4>
                    <p>AC broke down in the middle of the night? Heating failed during cold weather? Our <strong>emergency HVAC repair team</strong> is available 24/7/365.</p>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-2">
                                <i class="fa fa-clock-o fa-2x text-primary mb-2"></i>
                                <p class="mb-0 fw-bold">Response Time</p>
                                <small>Within 2 hours</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2">
                                <i class="fa fa-calendar fa-2x text-primary mb-2"></i>
                                <p class="mb-0 fw-bold">Available</p>
                                <small>24/7/365</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.3s">
                <div class="bg-primary text-white p-4 rounded mb-4">
                    <h4 class="text-white mb-3">Schedule Maintenance Today</h4>
                    <livewire:contact-form />
                </div>

                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="fa fa-shield-alt text-warning fa-2x float-end"></i>
                    <h6 class="mb-1">All Repairs Backed by</h6>
                    <h5>90-Day Warranty</h5>
                    <p class="small mb-0">Parts and labor warranty on all repair services.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection