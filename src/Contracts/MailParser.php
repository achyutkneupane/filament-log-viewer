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
 * @phpstan-type MailDetails array{
 *     plain: string,
 *     html: string,
 *     sender: array{name: string, email: string}|null,
 *     receiver: array{name: string, email: string}|null,
 *     subject: string,
 *     sent_date: string
 * }
 */
interface MailParser
{
    /**
     * @param  array{date: string, env: string, message: string}  $mailLine
     * @return LogRow|null
     */
    public function parse(array $mailLine, string $file): ?array;

    public function isMailStack(?string $logStack): bool;

    /**
     * @return MailDetails|null
     */
    public function parseRaw(string $raw): ?array;
}
