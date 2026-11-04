<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ $pageTitle ?? config('app.name', 'Laravel') }}</title>

    <meta name="description" content="Nickmark - Quality electronics, laptops, smartphones, tablets, and accessories at best prices.">
    <meta name="keywords" content="electronics, laptops, smartphones, tablets, accessories, online shopping, Nickmark">
    <meta name="author" content="Nickmark">
    <meta name="robots" content="index, follow">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph -->
    <meta property="og:site_name" content="Nickmark">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $ogTitle ?? config('app.name', 'Laravel') }}">
    <meta property="og:description" content="{{ $ogDescription ?? 'Nickmark - Quality electronics at best prices.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('assets/images/demos/demo-4/logo.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@nickmark">
    <meta name="twitter:title" content="{{ $twitterTitle ?? config('app.name', 'Laravel') }}">
    <meta name="twitter:description" content="{{ $twitterDescription ?? 'Nickmark - Quality electronics at best prices.' }}">
    <meta name="twitter:image" content="{{ $twitterImage ?? asset('assets/images/demos/demo-4/logo.png') }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/demos/demo-4/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Line Awesome Icons -->
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}">

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery.countdown.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/nouislider/nouislider.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/skins/skin-demo-4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demos/demo-4.css') }}">

    <!-- Scripts -->
    @routes
    @vite('resources/js/app.js')
    @inertiaHead

    <!-- JSON-LD Structured Data (rendered server-side for SEO) -->
    @if (isset($page->props['productJsonLd']))
        <script type="application/ld+json">{{ $page->props['productJsonLd'] }}</script>
    @endif
    @if (isset($page->props['breadcrumbJsonLd']))
        <script type="application/ld+json">{{ $page->props['breadcrumbJsonLd'] }}</script>
    @endif
</head>

<body class="font-sans antialiased">
    @inertia

    <!-- Plugins JS File -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.hoverIntent.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/superfish.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-input-spinner.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.plugin.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('assets/js/wNumb.js') }}"></script>
    <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/demos/demo-4.js') }}"></script>
</body>

</html>
