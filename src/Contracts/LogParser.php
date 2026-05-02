<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Generator;

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
interface LogParser
{
    /**
     * @return Generator<int, LogRow>
     */
    public function parse(string $filePath, string $fileName): Generator;

    public function isMailEntry(string $message): bool;
}
