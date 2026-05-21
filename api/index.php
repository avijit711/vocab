<?php

$dbPath = getenv('DB_DATABASE') ?: '';
if ($dbPath && !file_exists($dbPath) && str_starts_with($dbPath, '/tmp/')) {
    $dir = dirname($dbPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    touch($dbPath);
}

$_SERVER['APP_RUNNING_IN_VERCEL'] = 'true';

require __DIR__ . '/../public/index.php';
