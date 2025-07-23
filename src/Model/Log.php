<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;
use Sushi\Sushi;

final class Log extends Model
{
    use Sushi;

    /** @var string[] */
    protected array $schema = [
        'log_level' => 'string',
        'date' => 'datetime',
        'env' => 'string',
        'message' => 'string',
        'file' => 'string',
        'stack' => 'string',
    ];

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

    /** @return string[] */
    public function getRows(): array
    {
        $logFilePath = storage_path('logs');
        if (! is_dir($logFilePath)) {
            return [];
        }
        $files = scandir($logFilePath);

        $logs = [];

        foreach ($files as $file) {
            $filePath = $logFilePath.'/'.$file;
            if (! is_file($filePath)) {
                continue;
            }
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if ($lines === false) {
                continue;
            }

            $entryLines = [];

            foreach ($lines as $line) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line) && $entryLines !== []) {
                    $logs[] = $this->parseLogEntry($entryLines, $file);
                    $entryLines = [];
                }
                $entryLines[] = $line;
            }

            if ($entryLines !== []) {
                $logs[] = $this->parseLogEntry($entryLines, $file);
            }
        }

        return array_filter($logs);
    }

    protected function casts(): array
    {
        return [
            'log_level' => LogLevel::class,
            'stack' => 'json',
        ];
    }

    private function parseLogEntry(array $lines, string $file): ?array
    {
        $entry = implode("\n", $lines);

        preg_match('/\[(?<date>[\d\-:\s]+)\]\s(?<env>\w+)\.(?<level>\w+):\s(?<message>.*)/s', $entry, $matches);

        if (! isset($matches['level']) || ! isset($matches['message'])) {
            return null;
        }

        return [
            'date' => trim($matches['date']),
            'env' => trim($matches['env']),
            'log_level' => LogLevel::from(mb_strtolower(trim($matches['level']))),
            'message' => $this->extractMessage($matches['message']),
            'stack' => $this->extractStack($matches['message']),
            'file' => $file,
        ];
    }

    private function extractMessage(string $raw): string
    {
        $split = preg_split('/\n|\{/', $raw, 2);

        if (is_array($split) && isset($split[0])) {
            return trim($split[0]);
        }

        return trim($raw);
    }

    private function extractStack(string $raw): string
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

        return json_encode($stackTrace);
    }
}
