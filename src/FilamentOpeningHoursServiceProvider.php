<?php

namespace Theofanisv\FilamentOpeningHours;

use Illuminate\Support\ServiceProvider;

class FilamentOpeningHoursServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-opening-hours.php',
            'filament-opening-hours'
        );
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/filament-opening-hours.php'
                => config_path('filament-opening-hours.php'),
        ], 'filament-opening-hours-config');

        // Load translations
        $this->loadTranslationsFrom(
            __DIR__.'/../resources/lang',
            'filament-opening-hours'
        );

        // Publish translations
        $this->publishes([
            __DIR__.'/../resources/lang'
                => $this->app->langPath('vendor/filament-opening-hours'),
        ], 'filament-opening-hours-translations');
    }
}
