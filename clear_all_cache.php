<?php

// Manually clear Laravel opcache and caches
$paths = [
    'D:\Desktop-projects\nickmark-ecommerce\storage\framework\cache',
    'D:\Desktop-projects\nickmark-ecommerce\storage\framework\views',
    'D:\Desktop-projects\nickmark-ecommerce\bootstrap\cache',
];

foreach ($paths as $path) {
    if (is_dir($path)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->getExtension() === 'php' || $file->isDir()) {
                chmod($file, 0777);
                @unlink($file);
            }
        }
    }
}

// Clear config cache
$configCache = 'D:\Desktop-projects\nickmark-ecommerce\config\cache.php';
if (file_exists($configCache)) {
    chmod($configCache, 0777);
    unlink($configCache);
}

// Clear opcache