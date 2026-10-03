{{-- resources/views/components/why-choose-us-section.blade.php --}}
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <h1 class="display-6 mb-5">Why Customers Choose <strong>{{ config('app.name') }}</strong> for Their HVAC
                    Needs</h1>
                <p class="mb-5">At <strong>{{ config('app.name') }}</strong>, we've built our reputation on
                    reliability, transparency, and exceptional workmanship. Homeowners and businesses across the region
                    trust us because we deliver honest solutions that fit their budget and schedule. Here's what makes
                    us different.</p>
                <div class="d-flex mb-5">
                    <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 90px; height: 90px;">
                        <img class="img-fluid" src="{{ asset('img/icon/icon-08-light.png') }}"
                            alt="Trusted Service Icon">
                    </div>
                    <div class="ms-4">
                        <h5 class="mb-3">Licensed & Insured Professionals</h5>
                        <span>Every technician at <strong>{{ config('app.name') }}</strong> undergoes rigorous
                            background checks and continuous training. We're fully licensed, bonded, and insured —
                            giving you complete peace of mind when we work in your home.</span>
                    </div>
                </div>
                <div class="d-flex mb-5">
                    <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 90px; height: 90px;">
                        <img class="img-fluid" src="{{ asset('img/icon/icon-10-light.png') }}"
                            alt="Reasonable Price Icon">
                    </div>
                    <div class="ms-4">
                        <h5 class="mb-3">Upfront, Competitive Pricing</h5>
                        <span>No hidden fees, no surprise charges. <strong>{{ config('app.name') }}</strong> provides
                            detailed quotes before any work begins, so you know exactly what to expect. We offer
                            financing options and seasonal discounts to keep your home comfortable without stretching
                            your budget.</span>
                    </div>
                </div>
                <div class="d-flex mb-0">
                    <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 90px; height: 90px;">
                        <img class="img-fluid" src="{{ asset('img/icon/icon-06-light.png') }}" alt="24/7 Support Icon">
                    </div>
                    <div class="ms-4">
                        <h5 class="mb-3">Round-the-Clock Emergency Service</h5>
                        <span>HVAC emergencies don't wait for business hours. That's why
                            <strong>{{ config('app.name') }}</strong> offers 24/7/365 support. Our on-call technicians
                            arrive promptly — even on nights, weekends, and holidays — to restore your comfort
                            fast.</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="position-relative rounded overflow-hidden h-100" style="min-height: 400px;">
                    <img class="position-absolute w-100 h-100" src="{{ asset('img/cooling-services-in-nairobi.jpeg') }}"
                        alt="{{ config('app.name') }} professional HVAC technician servicing equipment"
                        style="object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>
