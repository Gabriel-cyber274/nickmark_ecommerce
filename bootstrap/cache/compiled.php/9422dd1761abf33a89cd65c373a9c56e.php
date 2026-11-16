<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia><?php echo e($pageTitle ?? config('app.name', 'Laravel')); ?></title>

    <meta name="description" content="Nickmark - Quality electronics, laptops, smartphones, tablets, and accessories at best prices.">
    <meta name="keywords" content="electronics, laptops, smartphones, tablets, accessories, online shopping, Nickmark">
    <meta name="author" content="Nickmark">
    <meta name="robots" content="index, follow">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Open Graph -->
    <meta property="og:site_name" content="Nickmark">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url('/')); ?>">
    <meta property="og:title" content="<?php echo e($ogTitle ?? config('app.name', 'Laravel')); ?>">
    <meta property="og:description" content="<?php echo e($ogDescription ?? 'Nickmark - Quality electronics at best prices.'); ?>">
    <meta property="og:image" content="<?php echo e($ogImage ?? asset('assets/images/demos/demo-4/logo.png')); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@nickmark">
    <meta name="twitter:title" content="<?php echo e($twitterTitle ?? config('app.name', 'Laravel')); ?>">
    <meta name="twitter:description" content="<?php echo e($twitterDescription ?? 'Nickmark - Quality electronics at best prices.'); ?>">
    <meta name="twitter:image" content="<?php echo e($twitterImage ?? asset('assets/images/demos/demo-4/logo.png')); ?>">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/demos/demo-4/logo.png')); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Line Awesome Icons -->
    <link rel="stylesheet"
        href="<?php echo e(asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css')); ?>">

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/owl-carousel/owl.carousel.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/jquery.countdown.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/nouislider/nouislider.css')); ?>">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/skins/skin-demo-4.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/demos/demo-4.css')); ?>">

    <!-- Scripts -->
    <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
    <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>

    <!-- JSON-LD Structured Data (rendered server-side for SEO) -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($page->props['productJsonLd'])): ?>
        <script type="application/ld+json"><?php echo e($page->props['productJsonLd']); ?></script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($page->props['breadcrumbJsonLd'])): ?>
        <script type="application/ld+json"><?php echo e($page->props['breadcrumbJsonLd']); ?></script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</head>

<body class="font-sans antialiased">
    <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } elseif (config('inertia.use_script_element_for_initial_page')) { ?><script data-page="app" type="application/json"><?php echo json_encode($page); ?></script><div id="app"></div><?php } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>

    <!-- Plugins JS File -->
    <script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery.hoverIntent.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery.waypoints.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/superfish.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap-input-spinner.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery.plugin.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery.countdown.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/wNumb.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/nouislider.min.js')); ?>"></script>

    <!-- Main JS File -->
    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/demos/demo-4.js')); ?>"></script>
</body>

</html>
<?php /**PATH D:\Desktop-projects\nickmark-ecommerce\resources\views/app.blade.php ENDPATH**/ ?>