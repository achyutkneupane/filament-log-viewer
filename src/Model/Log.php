<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Traits\HasMailLog;
use Generator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

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
 *     log_level: LogLevel,
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
    use HasMailLog;

    private static string $logFilePath = '';

    private static ?array $cachedRows = null;

    public static function destroyAllLogs(): void
    {
        $logDirectoryItems = self::getAllLogFiles();
        $logFilePath = self::getLogFilePath();

        foreach ($logDirectoryItems as $file) {
            $filePath = $logFilePath.'/'.$file;
            if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                file_put_contents($filePath, '');
            }
        }
    }

    /** @return array<int<0, max>, LogRow> */
    public static function getRows(bool $getCached = true): array
    {
        if ($getCached && self::$cachedRows !== null) {
            return self::$cachedRows;
        }

        $logs = [];
        $logDirectoryItems = self::getAllLogFiles();
        $logFilePath = self::getLogFilePath();

        foreach ($logDirectoryItems as $file) {
            $filePath = $logFilePath.DIRECTORY_SEPARATOR.$file;
            if (! is_file($filePath)) {
                continue;
            }
            if (pathinfo((string) $file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            foreach (self::processLogFile($filePath, $file) as $row) {
                $logs[] = $row;
            }
        }

        usort($logs, fn (array $a, array $b): int => $b['date'] <=> $a['date']);

        self::$cachedRows = $logs;

        return self::$cachedRows;
    }

    /** @return array<int<0, max>, LogRow> */
    public static function getLogsByLogLevel(string $logLevel = 'all-logs'): array
    {
        if ($logLevel === 'all-logs') {
            return self::getRows();
        }

        $logLevelWise = [];
        foreach (self::getRows() as $log) {
            /** @var LogLevel $logLevelEnum */
            $logLevelEnum = $log['log_level'];

            if ($logLevelEnum->value === $logLevel) {
                $logLevelWise[] = $log;
            }
        }

        return $logLevelWise;
    }

    public static function getLogCount(string $logLevel = 'all-logs'): ?int
    {
        $count = $logLevel === 'all-logs' ? count(self::getRows()) : count(self::getLogsByLogLevel($logLevel));

        return $count > 0 ? $count : null;
    }

    /** @return array<int, string> */
    public static function getAllLogFiles(): array
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return [];
        }

        /** @var int $configMaxFileSize */
        $configMaxFileSize = config('filament-log-viewer.max_log_file_size', 2048);
        $maxFileSize = $configMaxFileSize * 1024;

        $files = array_filter(
            self::getNestedFiles($logFilePath),
            fn (string $file): bool => filesize($logFilePath.'/'.$file) <= $maxFileSize
        );

        return array_map(fn (string $file): string => str_replace(storage_path(), '', $file), $files);
    }

    /** @return array<string, string|array<string, string>> */
    public static function getFilesForFilter(): array
    {
        $logFilePath = self::getAllLogFiles();

        /** @phpstan-var array<string, string|array<string, string>> */
        return (array) Collection::wrap($logFilePath)
            ->mapWithKeys(function (string $file): array {
                $filePath = str_replace(storage_path(), '', $file);

                return [$filePath => $filePath];
            })
            ->reduce(
                /**
                 * @param  array<string, string|array<string, string>>  $carry
                 * @return array<string, string|array<string, string>>
                 */
                function (array $carry, string $item): array {
                    if (str_contains($item, '/')) {
                        $parts = explode('/', $item);
                        $lastPart = array_pop($parts);
                        $directory = implode('/', $parts);

                        if (! array_key_exists($directory, $carry) || ! is_array($carry[$directory])) {
                            $carry[$directory] = [];
                        }

                        if (! is_array($carry[$directory])) {
                            $carry[$directory] = [];
                        }

                        $carry[$directory][$item] = $lastPart;
                    } else {
                        $carry[$item] = $item;
                    }

                    return $carry;
                }, []);
    }

    /**
     * @param string $rawMessage
     * @return list<StackTrace>
     */
    public static function getStackFromRaw(string $rawMessage): array
    {
        return self::extractStack($rawMessage);
    }

    /** @return list<StackTrace> */
    private static function extractStack(string $raw): array
    {
        /** @var list<StackTrace> */
        return app(Pipeline::class)
            ->send($raw)
            ->through([
                fn (string $raw, $next) => $next(explode("\n", $raw, 2)),
                function (array $parts, $next) {
                    if (! array_key_exists(1, $parts)) {
                        return $next(null);
                    }
                    /** @var string $tracePart */
                    $tracePart = $parts[1];

                    return $next(isset($tracePart) ? trim($tracePart) : null);
                },
                fn (?string $emptyOrParts, $next) => $next($emptyOrParts ? explode("\n", $emptyOrParts) : []),
                fn (array $stackTraceArray, $next) => $next(array_slice($stackTraceArray, 1, -1)),
                fn (array $slicedTrace, $next) => $next(array_map(fn ($item): array => ['trace' => $item], $slicedTrace)),
            ])
            ->thenReturn();
    }

    private static function hasStack(string $raw): bool
    {
        return str_contains($raw, '[stacktrace]') || str_contains($raw, '#0');
    }

    private static function getLogFilePath(): string
    {
        if (self::$logFilePath === '') {
            self::$logFilePath = storage_path('logs');
        }

        return self::$logFilePath;
    }

    /** @return list<string> */
    private static function getNestedFiles(string $directory): array
    {
        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.') {
                continue;
            }

            if ($item === '..') {
                continue;
            }

            $path = $directory.DIRECTORY_SEPARATOR.$item;
            $pathAfterRemovingStoragePath = str_replace(storage_path(), '', $path);
            $pathAfterRemovingFileName = str_replace(basename($path), '', $pathAfterRemovingStoragePath);
            $normalized = str_replace('\\', '/', $pathAfterRemovingFileName);
            $pathWithoutLogsPrefix = str_replace('/logs/', '', $normalized);

            if (is_dir($path)) {
                $files[] = self::getNestedFiles($path);
            } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'log') {
                $files[] = $pathWithoutLogsPrefix.basename($path);
            }
        }

        return $files;
    }

    /**
     * @return Generator<int, LogRow>
     */
    private static function processLogFile(string $filePath, string $file): Generator
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return;
        }

        $entryLines = [];

        while (($line = fgets($handle)) !== false) {
            $line = rtrim($line, "\r\n");

            if (($line[0] ?? '') === '[' && ($line[20] ?? '') === ']' && $entryLines !== []) {
                $parsed = self::parseLogEntry($entryLines, $file);
                if ($parsed) {
                    yield $parsed;
                }
                $entryLines = [];
            }
            $entryLines[] = $line;
        }

        if ($entryLines !== []) {
            $parsed = self::parseLogEntry($entryLines, $file);
            if ($parsed) {
                yield $parsed;
            }
        }

        fclose($handle);
    }

    /**
     * @param  array<int, string>  $lines
     * @return LogRow|null
     */
    private static function parseLogEntry(array $lines, string $file): ?array
    {
        $entry = implode("\n", $lines);

        preg_match('/\[(?<date>[\d\-:\s]+)\]\s(?<env>\w+)\.(?<level>\w+):\s(?<message>.*)/s', $entry, $matches);

        if (! isset($matches['level']) || ! isset($matches['message'])) {
            return null;
        }

        if (self::isMailStack($matches['message'])) {
            $mailLine = [
                'date' => $matches['date'] ?? '',
                'env' => $matches['env'] ?? '',
                'message' => $matches['message'],
            ];

            return self::parseMail($mailLine, $file);
        }

        $messagePart = trim($matches['message']);

        [$message, $description, $context] = self::splitMessagesAndContext($messagePart);

        return [
            'date' => $matches['date'] ? trim($matches['date']) : '',
            'env' => $matches['env'] ? trim($matches['env']) : '',
            'log_level' => LogLevel::from(mb_strtolower(trim($matches['level']))),
            'message' => $message,
            'description' => $description,
            'context' => $context,
            'mail' => null,
            'has_stack' => self::hasStack($matches['message']),
            'raw_stack' => $matches['message'],
            'file' => $file,
        ];
    }

    /** @return array{0: string, 1: string|null, 2: array<string, mixed>|null} */
    private static function splitMessagesAndContext(string $raw): array
    {
        $pattern = '/^(?<message>.*?)(?<json>\{.*\})$/s';

        if (preg_match($pattern, $raw, $matches)) {
            $message = trim($matches['message']);
            $json = trim($matches['json']);
            $decoded = json_decode($json, true);

            $jsonFirstLine = (string) strtok($json, "\n");

            $regex = '/"exception":"\[object\] \(.*?\(code: \d+\): (?<real_msg>.*?) (?<loc>at\s\/.*?)\)$/s';

            if (preg_match($regex, $jsonFirstLine, $stackMatches)) {
                $description = self::shortenPath(trim($stackMatches['loc']));
                $message = trim($stackMatches['real_msg']);
            } else {
                $description = null;
            }

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                $context = null;
            } else {
                /** @var array<string, mixed> $context */
                $context = array_map(fn ($value): mixed => is_string($value) && self::looksLikeJson($value)
                    ? json_decode($value, true) ?? $value
                    : $value,
                    $decoded
                );
            }

            return [$message, $description, $context];
        }

        return [trim($raw), null, null];
    }

    private static function looksLikeJson(string $value): bool
    {
        $value = trim($value);

        return
            (str_starts_with($value, '{') && str_ends_with($value, '}')) ||
            (str_starts_with($value, '[') && str_ends_with($value, ']'));
    }

    private static function shortenPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $basePath = base_path().DIRECTORY_SEPARATOR;

        return str_replace($basePath, '', $path);
    }
}
