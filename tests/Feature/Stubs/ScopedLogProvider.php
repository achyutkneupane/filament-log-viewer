<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;

/**
 * Route B: extend the default provider and override a protected hook.
 */
final class ScopedLogProvider extends LocalLogProvider
{
    protected function getAllLogFiles(): array
    {
        return ['laravel.log'];
    }
}
