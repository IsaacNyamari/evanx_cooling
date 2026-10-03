{{-- resources/views/components/about-section.blade.php --}}
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="h-100">
                    <h1 class="display-6 mb-5 w-100">Welcome to <strong>{{ config('app.name') }}</strong> — Your Trusted
                        Heating & Cooling Partner</h1>
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <img class="flex-shrink-0 me-3" src="{{ asset('img/icon/icon-07-primary.png') }}"
                                    alt="Expert Technician Icon">
                                <h5 class="mb-0">Expert Technicians</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <img class="flex-shrink-0 me-3" src="{{ asset('img/icon/icon-09-primary.png') }}"
                                    alt="Quality Service Icon">
                                <h5 class="mb-0">Premium Quality Service</h5>
                            </div>
                        </div>
                    </div>
                    <p class="mb-4">At <strong>{{ config('app.name') }}</strong>, we deliver reliable heating and
                        cooling solutions that keep your home comfortable all year round. Our certified technicians
                        bring years of experience to every job, ensuring professional installation, prompt repairs, and
                        preventative maintenance you can count on. We take pride in serving our community with honest
                        pricing, rapid response times, and a commitment to 100% customer satisfaction. Whether you need
                        emergency AC repair in July or a furnace tune-up before winter,
                        <strong>{{ config('app.name') }}</strong> is here to help.</p>

                    {{-- History Section --}}
                    <div class="bg-light p-4 rounded mb-4">
                        <h4 class="mb-3 text-primary">Our Story</h4>
                        <p class="mb-0"><strong>{{ config('app.name') }}</strong> was founded in 2023 by <strong>Evans
                                Macharia</strong>, a visionary entrepreneur and proud owner of <strong>Gas Line
                                Installation Kenya</strong>. Building on the success and reputation of his established
                            gas infrastructure company, Evans identified an opportunity to bring the same level of
                            excellence to the heating and cooling industry. Leveraging his deep industry knowledge,
                            technical expertise, and business acumen from leading Gas Line Installation Kenya, Evans
                            launched <strong>{{ config('app.name') }}</strong> as a natural extension of his commitment
                            to comfort and safety. Today, both companies operate under his leadership — with Gas Line
                            Installation Kenya serving the nation's gas infrastructure needs while
                            <strong>{{ config('app.name') }}</strong> delivers premium HVAC solutions to homes and
                            businesses. Together, they represent Evans Macharia's vision of complete, reliable energy
                            and climate control services for Kenya.</p>
                    </div>
                    {{-- Mission & Vision --}}
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="border-start border-primary border-4 ps-3">
                                <h5 class="mb-2"><i class="fa fa-bullseye text-primary me-2"></i> Our Mission</h5>
                                <p class="mb-0 small">To provide exceptional heating and cooling solutions that ensure
                                    every customer enjoys year-round comfort, energy efficiency, and peace of mind —
                                    delivered with honesty, expertise, and personalized care.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border-start border-primary border-4 ps-3">
                                <h5 class="mb-2"><i class="fa fa-eye text-primary me-2"></i> Our Vision</h5>
                                <p class="mb-0 small">To become Kenya's most trusted HVAC service provider, setting the
                                    standard for quality workmanship, innovation, and customer satisfaction while
                                    honoring the legacy of excellence passed down through generations.</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="btn-lg-square bg-primary rounded-circle me-3">
                                        <i class="fa fa-phone-alt text-white"></i>
                                    </div>
                                    <span class="mb-0">{{ config('site.phone') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center w-100">
                                    <div class="btn-lg-square bg-primary rounded-circle me-3">
                                        <i class="fa fa-envelope text-white"></i>
                                    </div>
                                    <span class="mb-0">{{ config('site.email') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                {{-- Equal Image Gallery using Bootstrap grid --}}
                <div class="row g-3">
                    <div class="col-6">
                        <img class="img-fluid w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{ asset('img/about-1.jpg') }}"
                            alt="HVAC technician from {{ config('app.name') }} at work"
                            style="aspect-ratio: 1 / 1; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img class="img-fluid w-100 wow zoomIn" data-wow-delay="0.3s"
                            src="{{ asset('img/about-2.jpg') }}"
                            alt="Professional cooling service by {{ config('app.name') }}"
                            style="aspect-ratio: 1 / 1; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img class="img-fluid w-100 wow zoomIn" data-wow-delay="0.5s"
                            src="{{ asset('img/about-3.jpg') }}"
                            alt="Heating system repair with {{ config('app.name') }}"
                            style="aspect-ratio: 1 / 1; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img class="img-fluid w-100 wow zoomIn" data-wow-delay="0.7s"
                            src="{{ asset('img/about-4.jpeg') }}"
                            alt="Customer satisfaction at {{ config('app.name') }}"
                            style="aspect-ratio: 1 / 1; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
