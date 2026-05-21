<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RunMigrations
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') === 'production' && !Schema::hasTable('migrations')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        return $next($request);
    }
}
