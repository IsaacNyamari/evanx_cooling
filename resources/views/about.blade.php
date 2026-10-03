@extends('app')

@section('title', 'About Us - ' . config('app.name') . ' | Professional HVAC Services in Nairobi, Kenya')

@section('meta_description', 'Learn about ' . config('app.name') . ', Nairobi\'s trusted HVAC company founded by Evans Macharia in 2023. Discover our mission, vision, and commitment to quality heating and cooling services.')

@section('meta_keywords', 'about HVAC company Nairobi, best cooling company Kenya, Evans Macharia, Gas Line Installation Kenya, trusted AC repair near me, HVAC experts story')

@section('og_title', 'About ' . config('app.name') . ' - Your Trusted HVAC Partner in Nairobi')

@section('og_description', 'Discover the story behind ' . config('app.name') . ', founded in 2023 with roots in Gas Line Installation Kenya. We deliver premium heating and cooling services across Nairobi.')

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