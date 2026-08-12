<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\Contracts\LogParser;
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
final class StubLogParser implements LogParser
{
    /**
     * @return Generator<int, LogRow>
     */
    public function parse(string $filePath, string $fileName): Generator
    {
        yield [
            'date' => '2025-02-02 00:00:00',
            'env' => 'staging',
            'log_level' => LogLevel::WARNING,
            'message' => 'Stub parser injected row',
            'description' => null,
            'mail' => null,
            'context' => null,
            'raw_stack' => '',
            'has_stack' => false,
            'file' => $fileName,
        ];
    }

    public function isMailEntry(string $message): bool
    {
        return false;
    }
}
