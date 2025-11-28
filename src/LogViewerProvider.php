<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class LogViewerProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/src/config/filament-log-viewer.php',
            'filament-log-viewer'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            dirname(__DIR__).'/src/resources/views',
            'filament-log-viewer'
        );
        $this->publishes([
            dirname(__DIR__).'/src/config/filament-log-viewer.php' => config_path('filament-log-viewer.php'),
        ], 'filament-log-viewer-config');
    }
}
