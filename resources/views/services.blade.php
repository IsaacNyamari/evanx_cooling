@extends('app')

@section('title', 'HVAC Services in Nairobi - ' . config('app.name') . ' | AC Installation, Cooling & Heating')

@section('meta_description', 'Explore professional HVAC services from ' . config('app.name') . ' in Nairobi. AC
    installation, cooling system repair, heating solutions, maintenance, indoor air quality, and annual inspections. Free
    quotes available.')

@section('meta_keywords', 'HVAC services Nairobi, AC installation near me, cooling system repair, heating services
    Kenya, air conditioner maintenance, indoor air quality solutions, annual HVAC inspection, best HVAC company near me')

@section('og_title', 'Complete HVAC Services - ' . config('app.name') . ' | Nairobi\'s Trusted Cooling & Heating
    Experts')

@section('og_description', 'From AC installation to annual inspections, ' . config('app.name') . ' offers comprehensive
    HVAC services in Nairobi. Certified technicians, 24/7 support.')

@section('content')
    <x-services />
    <x-features />

    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "name": "HVAC Services",
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
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "HVAC Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "AC Installation",
                        "description": "Professional air conditioner installation for residential and commercial properties.",
                        "url": "{{ url('/services/ac-installation') }}"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Cooling Services",
                        "description": "AC repair, maintenance, and emergency cooling system services.",
                        "url": "{{ url('/services/cooling-services') }}"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Heating Services",
                        "description": "Furnace installation, boiler repair, and heating system maintenance.",
                        "url": "{{ url('/services/heating-services') }}"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Maintenance & Repair",
                        "description": "Preventative maintenance plans and emergency repair services.",
                        "url": "{{ url('/services/maintenance-repair') }}"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Indoor Air Quality",
                        "description": "Air purifiers, humidifiers, and ventilation solutions.",
                        "url": "{{ url('/services/indoor-air-quality') }}"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Annual Inspections",
                        "description": "Comprehensive HVAC system inspections and safety checks.",
                        "url": "{{ url('/services/annual-inspections') }}"
                    }
                }
            ]
        }
    }
    @endverbatim
    </script>

    <script type="application/ld+json">
        @verbatim
            
       
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Services",
                "item": "{{ url('/services') }}"
            }
        ]
    } @endverbatim
    </script>
@endsection
