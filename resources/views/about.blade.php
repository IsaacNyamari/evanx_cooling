@extends('app')


@section('content')
    <x-about />

    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        "name": "About {{ config('app.name') }}",
        "description": "Learn about {{ config('app.name') }}, Nairobi's leading HVAC service provider founded in 2023.",
        "mainEntity": {
            "@type": "Organization",
            "name": "{{ config('app.name') }}",
            "foundingDate": "2023",
            "founder": {
                "@type": "Person",
                "name": "Evans Macharia"
            },
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Nairobi",
                "addressCountry": "Kenya"
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
                "name": "About Us",
                "item": "{{ url('/about') }}"
            }
        ]
    }
    @endverbatim
    </script>
@endsection