@extends('app')

@section('title', 'Contact Us - ' . config('app.name') . ' | HVAC Services in Nairobi | Free Quote')

@section('meta_description', 'Contact ' . config('app.name') . ' for professional HVAC services in Nairobi. Call +254 700 123 456, email us, or visit our Westlands office. Get a free quote for AC installation, repair, and maintenance.')

@section('meta_keywords', 'contact HVAC Nairobi, AC repair near me phone number, cooling services contact, heating company Nairobi, book AC service, emergency HVAC contact Nairobi')

@section('og_title', 'Contact ' . config('app.name') . ' - Get a Free HVAC Quote Today')

@section('og_description', 'Need AC installation or repair in Nairobi? Contact our certified HVAC technicians for fast, reliable service. Same-day appointments available.')

@section('canonical', url('/contact'))

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