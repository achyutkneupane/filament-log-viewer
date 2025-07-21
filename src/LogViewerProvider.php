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
    }
}
