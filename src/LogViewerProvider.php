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
use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;
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

        $this->app->bind(LogProvider::class, function (Application $app): LogProvider {
            /** @var LogParser $logParser */
            $logParser = $app->make(LogParser::class);

            /** @var StackTraceParser $stackTraceParser */
            $stackTraceParser = $app->make(StackTraceParser::class);

            return new LocalLogProvider($logParser, $stackTraceParser);
        });

        $this->app->bind(LogParser::class, function (Application $app): LogParser {
            /** @var MailParser $mailParser */
            $mailParser = $app->make(MailParser::class);

            /** @var StackTraceParser $stackTraceParser */
            $stackTraceParser = $app->make(StackTraceParser::class);

            return new FileLogParser($mailParser, $stackTraceParser);
        });

        $this->app->bind(MailParser::class, DefaultMailParser::class);
        $this->app->bind(StackTraceParser::class, DefaultStackTraceParser::class);
    }
}
