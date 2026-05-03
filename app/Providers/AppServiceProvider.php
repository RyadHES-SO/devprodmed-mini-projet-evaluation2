<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rating;
use App\Policies\RatingPolicy;
use Illuminate\Support\Facades\Gate;

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
        // On associe le modèle Rating à sa Policy
        Gate::policy(Rating::class, RatingPolicy::class);
    }
}
