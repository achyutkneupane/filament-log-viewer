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
            dirname(__DIR__).'/src/resources/views',
            'filament-log-viewer'
        );

        $this->loadTranslationsFrom(
            dirname(__DIR__).'/src/resources/lang',
            'filament-log-viewer'
        );
    }

    public function register(): void
    {
        $this->publishes([
            dirname(__DIR__).'/src/resources/lang' => resource_path('lang/vendor/filament-log-viewer'),
        ], 'filament-log-viewer-lang');
    }
}
