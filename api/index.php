<?php

$dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';

if ($dbConnection === 'sqlite') {
    $dbPath = getenv('DB_DATABASE') ?: '';
    if ($dbPath && !file_exists($dbPath) && str_starts_with($dbPath, '/tmp/')) {
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        touch($dbPath);
    }
}

$tmpDirs = ['/tmp/livewire-tmp'];
$storagePath = getenv('LARAVEL_STORAGE_PATH') ?: '';
if ($storagePath && str_starts_with($storagePath, '/tmp/')) {
    $tmpDirs[] = $storagePath;
    $tmpDirs[] = $storagePath.'/framework';
    $tmpDirs[] = $storagePath.'/framework/cache';
}
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

require __DIR__ . '/../public/index.php';

