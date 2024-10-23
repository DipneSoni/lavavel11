<?php

namespace App\Providers;

use App\Models\Owner;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Support\ServiceProvider;

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
        Owner::unguard();
        Patient::unguard();
        Treatment::unguard();
    }
}
