<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Foundation;
use App\Observers\FoundationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Foundation::observe(FoundationObserver::class);
    }
}
