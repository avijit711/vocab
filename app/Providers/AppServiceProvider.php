<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        try {
            DB::statement('select count(*) from migrations');
        } catch (\Throwable) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable) {
                //
            }
        }
    }
}
