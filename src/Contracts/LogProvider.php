<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts;

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
 * @phpstan-type StackTrace array{trace: string}
 */
interface LogProvider
{
    /**
     * @return array<int<0, max>, LogRow>
     */
    public function getRows(bool $refresh = false): array;

    /**
     * @return array<int<0, max>, LogRow>
     */
    public function getLogsByLevel(string $level): array;

    public function getCount(string $level = 'all-logs'): ?int;

    /**
     * @return array<int, string>
     */
    public function getFiles(): array;

    /**
     * @return array<string, string|array<string, string>>
     */
    public function getFilesForFilter(): array;

    public function deleteAll(): void;

    /**
     * @return list<StackTrace>
     */
    public function getStackFromRaw(string $rawStack): array;
}
