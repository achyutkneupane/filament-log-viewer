<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Providers;

use AchyutN\FilamentLogViewer\Contracts\LogParser;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Symfony\Component\Finder\Finder;

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
class LocalLogProvider implements LogProvider
{
    private string $logFilePath = '';

    /** @var list<LogRow>|null */
    private ?array $cachedRows = null;

    public function __construct(
        private readonly LogParser $logParser,
        private readonly StackTraceParser $stackTraceParser,
    ) {}

    public function deleteAll(): void
    {
        $this->resetCache();

        $logFilePath = $this->getLogFilePath();

        foreach ($this->getAllLogFiles() as $file) {
            $filePath = $logFilePath.DIRECTORY_SEPARATOR.$file;
            if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                file_put_contents($filePath, '');
            }
        }
    }

    /**
     * @return array<int<0, max>, LogRow>
     */
    public function getRows(bool $refresh = false): array
    {
        if ($refresh) {
            $this->resetCache();
        }

        if ($this->cachedRows !== null) {
            return $this->cachedRows;
        }

        $logs = [];
        $logFilePath = $this->getLogFilePath();

        foreach ($this->getAllLogFiles() as $file) {
            $filePath = $logFilePath.DIRECTORY_SEPARATOR.$file;
            if (! is_file($filePath)) {
                continue;
            }
            if (pathinfo((string) $file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            foreach ($this->logParser->parse($filePath, $file) as $row) {
                $logs[] = $row;
            }
        }

        usort($logs, fn (array $a, array $b): int => $b['date'] <=> $a['date']);

        $this->cachedRows = $logs;

        return $this->cachedRows;
    }

    /**
     * @return array<int<0, max>, LogRow>
     */
    public function getLogsByLevel(string $logLevel = 'all-logs'): array
    {
        if ($logLevel === 'all-logs') {
            return $this->getRows();
        }

        /** @var list<LogRow> */
        return collect($this->getRows())
            ->filter(fn (array $log): bool => $log['log_level']->value === $logLevel)
            ->values()
            ->toArray();
    }

    public function getCount(string $logLevel = 'all-logs'): ?int
    {
        $count = $logLevel === 'all-logs' ? count($this->getRows()) : count($this->getLogsByLevel($logLevel));

        return $count > 0 ? $count : null;
    }

    /**
     * @return array<int, string>
     */
    public function getFiles(): array
    {
        return $this->getAllLogFiles();
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    public function getFilesForFilter(): array
    {
        $initial = [];

        /** @var array<string, string|array<string, string>> */
        return collect($this->getAllLogFiles())
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
    public function getStackFromRaw(string $rawStack): array
    {
        return $this->stackTraceParser->extract($rawStack);
    }

    protected function getLogFilePath(): string
    {
        if ($this->logFilePath === '') {
            $this->logFilePath = storage_path('logs');
        }

        return $this->logFilePath;
    }

    /**
     * @return list<string>
     */
    protected function getAllLogFiles(): array
    {
        $logFilePath = $this->getLogFilePath();

        if (! is_dir($logFilePath)) {
            return [];
        }

        $maxFileSize = config()->integer('filament-log-viewer.max_log_file_size', 2048) * 1024;

        /** @var list<string> */
        return collect($this->getNestedFiles($logFilePath))
            ->filter(
                fn (string $file): bool => file_exists($logFilePath.DIRECTORY_SEPARATOR.$file) && filesize($logFilePath.DIRECTORY_SEPARATOR.$file) <= $maxFileSize
            )
            ->values()
            ->toArray();
    }

    /**
     * @return list<string>
     */
    protected function getNestedFiles(string $directory): array
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

    private function resetCache(): void
    {
        $this->cachedRows = null;
    }
}
