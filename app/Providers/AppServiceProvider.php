<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // We only want one instance of faker, this allows
        // us to use unique() to prevent data clashes.
        $this->app->singleton('faker', function ($app) {
            return \Faker\Factory::create();
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

}
