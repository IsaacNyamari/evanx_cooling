@extends('app')

@section('title', config('app.name') . ' - Professional HVAC Services in Nairobi, Kenya | AC Installation, Repair &
    Maintenance')

@section('meta_description', 'Looking for trusted HVAC services in Nairobi? ' . config('app.name') . ' offers
    professional AC installation, cooling system repair, heating services, and preventative maintenance. Certified
    technicians, 24/7 emergency support. Get a free quote today!')

@section('meta_keywords', 'HVAC services Nairobi, AC installation near me, cooling systems repair, heating services
    Kenya, air conditioner repair, HVAC experts Nairobi, best HVAC company near me, AC maintenance Nairobi')

@section('meta_author', config('app.name'))

@section('og_title', config('app.name') . ' - Best Heating & Cooling Services in Nairobi')

@section('og_description', 'Professional HVAC services including AC installation, cooling repair, heating solutions, and
    indoor air quality improvement. Certified technicians serving Nairobi and surrounding areas.')

@section('og_image', asset('img/og-image.jpg'))

@section('twitter_card', 'summary_large_image')

@section('canonical', url()->current())

@section('content')
    <!-- Carousel Start -->
    <x-main-carousel />
    <!-- Carousel End -->

    <!-- About Start -->
    <x-about />
    <!-- About End -->

    <!-- Features Start -->
    <x-features />
    <!-- Features End -->

    <!-- Service Start -->
    <x-services />
    <!-- Service End -->

    <!-- Contact Start -->
    <x-contact />
    <!-- Contact End -->

    @verbatim
        <script type="application/ld+json">
        @verbatim
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "{{ config('app.name') }}",
        "image": "{{ asset('img/logo.png') }}",
        "logo": "{{ asset('img/logo.png') }}",
        "url": "{{ url('/') }}",
        "telephone": "{{ config('site.phone') }}",
        "email": "info@evanxcoolingsystems.co.ke",
        "priceRange": "KES",
        "description": "Professional HVAC services in Nairobi offering AC installation, cooling system repair, heating services, and preventative maintenance. Certified technicians with 24/7 emergency support.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Westlands",
            "addressLocality": "Nairobi",
            "addressRegion": "Nairobi",
            "addressCountry": "KE"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": -1.286389,
            "longitude": 36.817223
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens": "08:00",
                "closes": "18:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Saturday",
                "opens": "09:00",
                "closes": "16:00"
            }
        ],
        "sameAs": [
            "https://www.facebook.com/evanscoolingsystems",
            "https://twitter.com/evanscooling",
            "https://www.instagram.com/evanscoolingsystems",
            "https://www.linkedin.com/company/evans-cooling-systems"
        ],
        "paymentAccepted": ["Cash", "M-Pesa", "Bank Transfer", "Credit Card"],
        "areaServed": {
            "@type": "City",
            "name": "Nairobi",
            "containedInPlace": {
                "@type": "Country",
                "name": "Kenya"
            }
        },
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "HVAC Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "AC Installation",
                        "description": "Professional air conditioner installation for residential and commercial properties."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Cooling Services",
                        "description": "AC repair, maintenance, and emergency cooling system services."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Heating Services",
                        "description": "Furnace installation, boiler repair, and heating system maintenance."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Maintenance & Repair",
                        "description": "Preventative maintenance plans and emergency repair services."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Indoor Air Quality",
                        "description": "Air purifiers, humidifiers, and ventilation solutions."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Annual Inspections",
                        "description": "Comprehensive HVAC system inspections and safety checks."
                    }
                }
            ]
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "127",
            "bestRating": "5",
            "worstRating": "1"
        }
    }
    @endverbatim
    </script>


        <script type="application/ld+json">
        @verbatim
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ config('app.name') }}",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/search') }}?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
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
            }
        ]
    }
    @endverbatim
    </script>
    @endsection

@section('footer_scripts')
    <script type="text/javascript">
        // Basic analytics or tracking code can go here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('{{ config('app.name') }} - Professional HVAC Services in Nairobi');
        });
    </script>
@endsection
