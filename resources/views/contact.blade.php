@extends('app')


@section('content')
    <x-contact />

    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Contact {{ config('app.name') }}",
        "description": "Get in touch with {{ config('app.name') }} for professional HVAC services in Nairobi.",
        "mainEntity": {
            "@type": "LocalBusiness",
            "name": "{{ config('app.name') }}",
            "telephone": "{{ config('site.phone') }}",
            "email": "info@evanxcoolingsystems.co.ke",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Westlands",
                "addressLocality": "Nairobi",
                "addressCountry": "KE"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "{{ config('site.phone') }}",
                "contactType": "customer service",
                "availableLanguage": ["English", "Swahili"],
                "hoursAvailable": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                    "opens": "08:00",
                    "closes": "18:00"
                }
            }
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
                "name": "Contact Us",
                "item": "{{ url('/contact') }}"
            }
        ]
    }
    @endverbatim
    </script>
@endsection