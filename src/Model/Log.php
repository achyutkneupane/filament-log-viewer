<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Traits\HasMailLog;
use Carbon\Carbon;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

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
            if (is_file($filePath) && pathinfo((string) $file, PATHINFO_EXTENSION) === 'log') {
                file_put_contents($filePath, '');
            }
        }
    }

    /** @return array<string, array<string, string>> */
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

    public static function getLogsByLogLevel(string $logLevel = 'all-logs'): array
    {
        if ($logLevel === 'all-logs') {
            return self::getRows();
        }

        $logLevelWise = [];
        foreach (self::getRows() as $log) {
            $logHasLogLevel = array_key_exists('log_level', $log) && $log['log_level'] instanceof LogLevel;
            if ($logHasLogLevel && $log['log_level']->value === $logLevel) {
                $logLevelWise[] = $log;
            }
        }

        return $logLevelWise;
    }

    public static function getLogCount(string $logLevel = 'all-logs'): int
    {
        if ($logLevel === 'all-logs') {
            return count(self::getRows());
        }

        return count(self::getLogsByLogLevel($logLevel));
    }

    public static function getAllLogFiles(): array
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return [];
        }

        $files = self::getNestedFiles($logFilePath);

        return array_map(fn ($file): string|array => str_replace(storage_path(), '', $file), $files);
    }

    public static function getFilesForFilter(): array
    {
        $logFilePath = self::getAllLogFiles();

        return Collection::wrap($logFilePath)
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
            $pathWithoutLogsPrefix = str_replace('/logs/', '', $pathAfterRemovingFileName);

            if (is_dir($path)) {
                $files = array_merge($files, self::getNestedFiles($path));
            } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'log') {
                $files[] = $pathWithoutLogsPrefix.basename($path);
            }
        }

        return $files;
    }

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

    private static function parseLogEntry(array $lines, string $file): ?array
    {
        $entry = implode("\n", $lines);

        preg_match('/\[(?<date>[\d\-:\s]+)\]\s(?<env>\w+)\.(?<level>\w+):\s(?<message>.*)/s', $entry, $matches);

        if (! isset($matches['level']) || ! isset($matches['message'])) {
            return null;
        }

        if (self::isMailStack($matches['message'])) {
            return self::parseMail($matches, $file);
        }

        return [
            'date' => trim($matches['date']),
            'env' => trim($matches['env']),
            'log_level' => LogLevel::from(mb_strtolower(trim($matches['level']))),
            'message' => self::extractMessage($matches['message']),
            'stack' => self::extractStack($matches['message']),
            'file' => $file,
        ];
    }

    private static function extractMessage(string $raw): string
    {
        $split = preg_split('/[\n{]/', $raw, 2);

        if (is_array($split) && isset($split[0])) {
            return trim($split[0]);
        }

        return trim($raw);
    }

    private static function extractStack(string $raw): string
    {
        $stackTrace = app(Pipeline::class)
            ->send($raw)
            ->through([
                fn (string $raw, $next) => $next(explode("\n", $raw, 2)),
                fn ($parts, $next) => $next(isset($parts[1]) ? trim($parts[1]) : null),
                function ($emptyOrParts, $next) {
                    if (empty($emptyOrParts)) {
                        return null;
                    }

                    return $next($emptyOrParts);
                },
                fn ($emptyOrParts, $next) => $next(explode("\n", (string) $emptyOrParts)),
                fn ($stackTraceArray, $next) => $next(array_slice($stackTraceArray, 1, -1)),
                fn ($slicedTrace, $next) => $next(array_map(fn ($item): array => ['trace' => $item], $slicedTrace)),
            ])
            ->thenReturn();

        if (empty($stackTrace)) {
            return json_encode([]);
        }

        return json_encode($stackTrace);
    }
}
