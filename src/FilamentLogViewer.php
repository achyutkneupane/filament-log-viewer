<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class FilamentLogViewer implements Plugin
{
    public function getId(): string
    {
        return 'filament-log-viewer';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([]);
    }

    public static function make(): FilamentLogViewer
    {
        return app(FilamentLogViewer::class);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
