<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { font-family: 'Figtree', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 20px; }
        .error-container { text-align: center; max-width: 480px; }
        .error-code { font-size: 12rem; font-weight: 700; line-height: 1; color: #e2e8f0; position: relative; }
        .error-code::after { content: ''; position: absolute; bottom: -0.5em; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background: linear-gradient(90deg, #3b82f6, #6366f1); border-radius: 2px; }
        .error-title { font-size: 2rem; font-weight: 600; color: #1e293b; margin-top: 1rem; }
        .error-message { font-size: 1.125rem; color: #64748b; margin-top: 0.75rem; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 2rem; padding: 0.875rem 2rem; background: linear-gradient(90deg, #3b82f6, #2563eb); color: white; font-weight: 600; font-size: 1rem; border-radius: 0.75rem; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
        .icon { width: 80px; height: 80px; margin: 0 auto 1.5rem; opacity: 0.15; }
        @media (max-width: 640px) { .error-code { font-size: 7rem; } .error-title { font-size: 1.5rem; } }
    </style>
</head>
<body>
    <div class="error-container">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#3b82f6"/>
            <path d="M12 8V12M12 16H12.01" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">Sorry, we couldn't find the page you're looking for. It might have been moved or doesn't exist.</p>
        <a href="<?php echo e(url('/')); ?>" class="btn">Go Home</a>
    </div>
</body>
</html><?php /**PATH D:\Desktop-projects\nickmark-ecommerce\resources\views/errors/404.blade.php ENDPATH**/ ?>