<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class FilamentLogViewer implements Plugin
{
    public static function make(): self
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'filament-log-viewer';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
