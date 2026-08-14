<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;

/**
 * A read-only provider (e.g. a Pail tail or a cloud reader) that does not
 * implement CanDeleteLogs, so the clear actions must stay hidden.
 */
final class ReadOnlyLogProvider implements LogProvider
{
    public function getRows(bool $refresh = false): array
    {
        return [];
    }

    public function getLogsByLevel(string $level): array
    {
        return [];
    }

    public function getCount(string $level = 'all-logs'): ?int
    {
        return null;
    }

    public function getFiles(): array
    {
        return ['stream.log'];
    }

    public function getFilesForFilter(): array
    {
        return ['stream.log' => 'stream.log'];
    }

    public function getStackFromRaw(string $rawStack): array
    {
        return [];
    }
}
