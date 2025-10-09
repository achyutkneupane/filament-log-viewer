<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Traits\HasMailLog;
use Carbon\Carbon;
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
 *     mail: MailDetails|null,
 *     context: array<string, mixed>|null,
 *     stack: StackTrace[],
 *     file: string
 * }
 */
final class Log
{
    use HasMailLog;

    private static string $logFilePath = '';

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
    public static function getRows(): array
    {
        $logs = [];
        $logDirectoryItems = self::getAllLogFiles();
        $logFilePath = self::getLogFilePath();

        foreach ($logDirectoryItems as $file) {
            $filePath = $logFilePath.'/'.$file;
            if (! is_file($filePath)) {
                continue;
            }
            if (pathinfo((string) $file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $logs = array_merge($logs, self::processLogFile($filePath, $file));
        }

        usort($logs, function (array $a, array $b): int {
            $dateA = Carbon::parse($a['date']);
            $dateB = Carbon::parse($b['date']);

            return $dateB->timestamp <=> $dateA->timestamp;
        });

        return array_filter($logs);
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

            $logHasLogLevel = array_key_exists('log_level', $log);
            if ($logLevelEnum->value === $logLevel) {
                $logLevelWise[] = $log;
            }
        }

        return $logLevelWise;
    }

    public static function getLogCount(string $logLevel = 'all-logs'): ?int
    {
        $count = $logLevel === 'all-logs' ? count(self::getRows()) : count(self::getLogsByLogLevel($logLevel));

        return $count === 0 ? null : $count;
    }

    /** @return array<int, string> */
    public static function getAllLogFiles(): array
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return [];
        }

        $files = self::getNestedFiles($logFilePath);

        return array_map(fn (string $file): string => str_replace(storage_path(), '', $file), $files);
    }

    /** @return array<string, string|array<string, string>> */
    public static function getFilesForFilter(): array
    {
        $logFilePath = self::getAllLogFiles();

        return (array) Collection::wrap($logFilePath)
            ->mapWithKeys(function (string $file): array {
                $filePath = str_replace(storage_path(), '', $file);

                return [$filePath => $filePath];
            })
            ->reduce(function ($carry, $item) {
                if (str_contains($item, '/')) {
                    $parts = explode('/', $item);
                    $lastPart = array_pop($parts);
                    $directory = implode('/', $parts);

                    if (! isset($carry[$directory])) {
                        $carry[$directory] = [];
                    }

                    $carry[$directory][$item] = $lastPart;
                } else {
                    $carry[$item] = $item;
                }

                return $carry;
            }, []);
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
                $files = array_merge($files, self::getNestedFiles($path));
            } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'log') {
                $files[] = $pathWithoutLogsPrefix.basename($path);
            }
        }

        return $files;
    }

    /**
     * @return array<int<0, max>, LogRow>
     */
    private static function processLogFile(string $filePath, string $file): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return [];
        }

        $logs = [];
        $entryLines = [];

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line) && $entryLines !== []) {
                $logs[] = self::parseLogEntry($entryLines, $file);
                $entryLines = [];
            }
            $entryLines[] = $line;
        }

        if ($entryLines !== []) {
            $logs[] = self::parseLogEntry($entryLines, $file);
        }

        return array_filter($logs);
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

        [$message, $context] = self::splitMessageAndContext($messagePart);

        return [
            'date' => array_key_exists('date', $matches) ? trim($matches['date']) : '',
            'env' => array_key_exists('env', $matches) ? trim($matches['env']) : '',
            'log_level' => LogLevel::from(mb_strtolower(trim($matches['level']))),
            'message' => $message,
            'context' => $context,
            'mail' => null,
            'stack' => self::extractStack($matches['message']),
            'file' => $file,
        ];
    }

    /** @return array{0: string, 1: array<string, mixed>|null} */
    private static function splitMessageAndContext(string $raw): array
    {
        $pattern = '/^(?<message>.*?)(?<json>\{.*\})$/s';

        if (preg_match($pattern, $raw, $matches)) {
            $json = trim($matches['json']);
            $decoded = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return [trim($matches['message']), null];
            }

            $loopParsed = array_map(fn ($value): mixed => is_string($value) && self::looksLikeJson($value) ? json_decode($value, true) ?? $value : $value, $decoded);

            return [
                trim($matches['message']),
                $loopParsed,
            ];
        }

        return [$raw, null];
    }

    private static function looksLikeJson(string $value): bool
    {
        $value = trim($value);

        return
            (str_starts_with($value, '{') && str_ends_with($value, '}')) ||
            (str_starts_with($value, '[') && str_ends_with($value, ']'));
    }

    /** @return list<StackTrace> */
    private static function extractStack(string $raw): array
    {
        return app(Pipeline::class)
            ->send($raw)
            ->through([
                fn (string $raw, $next) => $next(explode("\n", $raw, 2)),
                fn ($parts, $next) => $next(isset($parts[1]) ? trim($parts[1]) : null),
                fn ($emptyOrParts, $next) => $next(explode("\n", (string) $emptyOrParts)),
                fn ($stackTraceArray, $next) => $next(array_slice($stackTraceArray, 1, -1)),
                fn ($slicedTrace, $next) => $next(array_map(fn ($item): array => ['trace' => $item], $slicedTrace)),
            ])
            ->thenReturn();
    }
}
