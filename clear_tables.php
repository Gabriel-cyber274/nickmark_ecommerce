<?php
$dir = 'D:\Desktop-projects\nickmark-ecommerce\app\Filament\Resources\GeneralSettings\Tables';
if (is_dir($dir)) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_BAD_LINKS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        chmod($file, 0777);
        @unlink($file);
    }
    chmod($dir, 0777);
    @rmdir($dir);
    echo "Deleted successfully";
} else {
    echo "Directory does not exist";
}