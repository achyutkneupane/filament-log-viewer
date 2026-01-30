<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Traits\HasMailLog;
use Generator;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

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

    /** @var list<LogRow>|null */
    private static ?array $cachedRows = null;

    public static function destroyAllLogs(): void
    {
        self::resetCache();

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
        if (! $getCached) {
            self::resetCache();
        }

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

        /** @var list<LogRow> */
        return collect(self::getRows())
            ->filter(fn (array $log): bool => $log['log_level']->value === $logLevel)
            ->values()
            ->toArray();
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

        $maxFileSize = config()->integer('filament-log-viewer.max_log_file_size', 2048) * 1024;

        /** @var list<string> */
        return collect(self::getNestedFiles($logFilePath))
            ->filter(
                fn (string $file): bool => file_exists($logFilePath.DIRECTORY_SEPARATOR.$file) && filesize($logFilePath.DIRECTORY_SEPARATOR.$file) <= $maxFileSize
            )
            ->values()
            ->toArray();
    }

    /** @return array<string, string|array<string, string>> */
    public static function getFilesForFilter(): array
    {
        $initial = [];

        /** @var array<string, string|array<string, string>> */
        return collect(self::getAllLogFiles())
            ->reduce(function (array $carry, string $file): array {
                if (str_contains($file, DIRECTORY_SEPARATOR)) {
                    $directory = dirname($file);
                    $filename = basename($file);

                    if (! isset($carry[$directory]) || ! is_array($carry[$directory])) {
                        $carry[$directory] = [];
                    }

                    $carry[$directory][$file] = $filename;
                } else {
                    $carry[$file] = $file;
                }

                return $carry;
            }, $initial);
    }

    /**
     * @return list<StackTrace>
     */
    public static function getStackFromRaw(string $rawMessage): array
    {
        return self::extractStack($rawMessage);
    }

    private static function resetCache(): void
    {
        self::$cachedRows = null;
    }

    /** @return list<StackTrace> */
    private static function extractStack(string $raw): array
    {
        $parts = explode("\n", $raw, 2);

        if (! isset($parts[1])) {
            return [];
        }

        $tracePart = trim($parts[1]);
        if ($tracePart === '' || $tracePart === '0') {
            return [];
        }

        $lines = explode("\n", $tracePart);

        $count = count($lines);
        if ($count <= 1) {
            return [];
        }

        $result = [];
        $end = $count - 1;

        for ($i = 1; $i < $end; $i++) {
            $line = trim($lines[$i]);
            if ($line !== '') {
                $result[] = ['trace' => $line];
            }
        }

        return $result;
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
        if (! is_dir($directory)) {
            return [];
        }

        $finder = Finder::create()
            ->files()
            ->name('*.log')
            ->in($directory);

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRelativePathname();
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
            'date' => trim($matches['date'] ?? ''),
            'env' => trim($matches['env'] ?? ''),
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

        return Str::of($path)->after(base_path().DIRECTORY_SEPARATOR)->toString();
    }
}
