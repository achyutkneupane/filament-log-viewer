<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Contracts\LogParser;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\MailParser;
use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\FileLogParser;
use Illuminate\Contracts\Foundation\Application;
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

        $this->app->singleton(
            FilamentLogViewer::class,
            fn (): FilamentLogViewer => new FilamentLogViewer()
        );

        $this->app->singleton(LogProvider::class, function (Application $app): LogProvider {
            /** @var FilamentLogViewer $plugin */
            $plugin = $app->make(FilamentLogViewer::class);

            /** @var class-string<LogProvider> $class */
            $class = $plugin->getProviderClass();

            /** @var LogProvider $provider */
            $provider = $app->make($class);

            return $provider;
        });

        $this->app->singleton(LogParser::class, fn (Application $app): LogParser => new FileLogParser($app->make(MailParser::class), $app->make(StackTraceParser::class)));

        $this->app->bind(MailParser::class, DefaultMailParser::class);
        $this->app->bind(StackTraceParser::class, DefaultStackTraceParser::class);
    }
}
