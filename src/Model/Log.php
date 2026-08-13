<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use Illuminate\Support\Facades\App;

/**
 * @phpstan-type MailDetails array{
 *     plain: string,
 *     html: string,
 *     sender: array{name: string, email: string}|null,
 *     receiver: array{name: string, email: string}|null,
 *     subject: string,
 *     sent_date: string
 * }
 * @phpstan-type StackTrace array{trace: string}
 * @phpstan-type LogRow array{
 *     date: string,
 *     env: string,
 *     log_level: \AchyutN\FilamentLogViewer\Enums\LogLevel,
 *     message: string,
 *     description: string|null,
 *     mail: MailDetails|null,
 *     context: array<string, mixed>|null,
 *     raw_stack: string,
 *     has_stack: bool,
 *     file: string
 * }
 */
final class Log
{
    public static function destroyAllLogs(): void
    {
        self::provider()->deleteAll();
    }

    /** @return array<int<0, max>, LogRow> */
    public static function getRows(bool $getCached = true): array
    {
        return self::provider()->getRows(! $getCached);
    }

    /** @return array<int<0, max>, LogRow> */
    public static function getLogsByLogLevel(string $logLevel = 'all-logs'): array
    {
        return self::provider()->getLogsByLevel($logLevel);
    }

    public static function getLogCount(string $logLevel = 'all-logs'): ?int
    {
        return self::provider()->getCount($logLevel);
    }

    /** @return array<int, string> */
    public static function getAllLogFiles(): array
    {
        return self::provider()->getFiles();
    }

    /** @return array<string, string|array<string, string>> */
    public static function getFilesForFilter(): array
    {
        return self::provider()->getFilesForFilter();
    }

    /**
     * @return list<StackTrace>
     */
    public static function getStackFromRaw(string $rawMessage): array
    {
        return self::provider()->getStackFromRaw($rawMessage);
    }

    private static function provider(): LogProvider
    {
        /** @var LogProvider */
        return App::make(LogProvider::class);
    }
}
