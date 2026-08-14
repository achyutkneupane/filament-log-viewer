<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\Contracts\CanDeleteLogs;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Enums\LogLevel;

/**
 * @phpstan-type LogRow array{
 *     date: string,
 *     env: string,
 *     log_level: LogLevel,
 *     message: string,
 *     description: string|null,
 *     mail: array{plain: string, html: string, sender: array{name: string, email: string}|null, receiver: array{name: string, email: string}|null, subject: string, sent_date: string}|null,
 *     context: array<string, mixed>|null,
 *     raw_stack: string,
 *     has_stack: bool,
 *     file: string
 * }
 */
final class StubLogProvider implements CanDeleteLogs, LogProvider
{
    public function getRows(bool $refresh = false): array
    {
        /** @var LogRow $row */
        $row = [
            'date' => '2025-01-01 00:00:00',
            'env' => 'local',
            'log_level' => LogLevel::INFO,
            'message' => 'Stub provider injected row',
            'description' => null,
            'mail' => null,
            'context' => null,
            'raw_stack' => '',
            'has_stack' => false,
            'file' => 'stub.log',
        ];

        return [$row];
    }

    public function getLogsByLevel(string $level): array
    {
        return $this->getRows();
    }

    public function getCount(string $level = 'all-logs'): ?int
    {
        $count = count($this->getRows());

        return $count > 0 ? $count : null;
    }

    public function getFiles(): array
    {
        return ['stub.log'];
    }

    public function getFilesForFilter(): array
    {
        return ['stub.log' => 'stub.log'];
    }

    public function deleteAll(): void {}

    public function deleteFile(string $file): void {}

    public function getStackFromRaw(string $rawStack): array
    {
        return [];
    }
}
