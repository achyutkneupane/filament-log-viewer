<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Traits\HasMailLog;
use Illuminate\Pipeline\Pipeline;

final class Log
{
    use HasMailLog;

    public static function destroyAllLogs(): void
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return;
        }
        $files = scandir($logFilePath);

        foreach ($files as $file) {
            $filePath = $logFilePath.'/'.$file;
            if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                file_put_contents($filePath, '');
            }
        }
    }

    /** @return array<string, array<string, string>> */
    public static function getRows(): array
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return [];
        }

        $logs = [];

        foreach (self::getNestedFiles($logFilePath) as $file) {
            $filePath = $logFilePath.'/'.$file;
            if (! is_file($filePath)) {
                continue;
            }
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $logs = array_merge($logs, self::processLogFile($filePath, $file));
        }

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

    private static function getNestedFiles(string $directory): array
    {
        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory.DIRECTORY_SEPARATOR.$item;

            if (is_dir($path)) {
                $files = array_merge($files, self::getNestedFiles($path));
            } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'log') {
                $files[] = $item;
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
