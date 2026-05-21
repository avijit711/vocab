<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

$dbPath = getenv('DB_DATABASE') ?: '';
if ($dbPath && !file_exists($dbPath) && str_starts_with($dbPath, '/tmp/')) {
    $dir = dirname($dbPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    touch($dbPath);
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('DB_CONNECTION') === 'sqlite' && !\Illuminate\Support\Facades\Schema::hasTable('migrations')) {
    $app->make(Illuminate\Contracts\Console\Kernel::class)->call('migrate', ['--force' => true]);
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
