<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Contracts\LogParser;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\MailParser;
use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;
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

        $this->app->singleton(LogParser::class, function (Application $app): LogParser {
            /** @var FilamentLogViewer $plugin */
            $plugin = $app->make(FilamentLogViewer::class);

            /** @var class-string<LogParser> $class */
            $class = $plugin->getParserClass();

            /** @var LogParser $parser */
            $parser = $app->make($class);

            return $parser;
        });

        $this->app->bind(MailParser::class, function (Application $app): MailParser {
            /** @var FilamentLogViewer $plugin */
            $plugin = $app->make(FilamentLogViewer::class);

            /** @var class-string<MailParser> $class */
            $class = $plugin->getMailParserClass();

            /** @var MailParser $mailParser */
            $mailParser = $app->make($class);

            return $mailParser;
        });

        $this->app->bind(StackTraceParser::class, function (Application $app): StackTraceParser {
            /** @var FilamentLogViewer $plugin */
            $plugin = $app->make(FilamentLogViewer::class);

            /** @var class-string<StackTraceParser> $class */
            $class = $plugin->getStackTraceParserClass();

            /** @var StackTraceParser $stackTraceParser */
            $stackTraceParser = $app->make($class);

            return $stackTraceParser;
        });
    }
}
