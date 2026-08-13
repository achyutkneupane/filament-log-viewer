<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\LogTable;

/**
 * Route B (page): extend the default page and override a protected factory.
 */
final class CustomLogTable extends LogTable
{
    public static function customMarker(): string
    {
        return 'custom-log-table-marker';
    }
}
