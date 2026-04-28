<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class LogViewerProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            dirname(__DIR__).'/resources/views',
            'filament-log-viewer'
        );

        $this->loadTranslationsFrom(
            dirname(__DIR__).'/resources/lang',
            'filament-log-viewer'
        );

        $this->publishes([
            dirname(__DIR__).'/resources/lang' => resource_path('lang/vendor/filament-log-viewer'),
        ], 'filament-log-viewer-lang');

        $this->publishes([
            dirname(__DIR__).'/config/filament-log-viewer.php' => config_path('filament-log-viewer.php'),
        ], 'filament-log-viewer-config');
    }

    public function register(): void
    {

        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/filament-log-viewer.php',
            'filament-log-viewer'
        );
    }
}
