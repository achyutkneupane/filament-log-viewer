<?php

namespace AchyutN\FilamentLogViewer\Model;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;
use Sushi\Sushi;

class Log extends Model
{
    use Sushi;

    protected function casts(): array
    {
        return [
            'log_level' => LogLevel::class,
            'stack' => 'json'
        ];
    }

    protected array $schema = [
        'log_level' => 'string',
        'date' => 'datetime',
        'env' => 'string',
        'message' => 'string',
        'file' => 'string',
        'stack' => 'string',
    ];

    public function getRows(): array
    {
        $logFilePath = storage_path('logs');
        if (!is_dir($logFilePath)) {
            return [];
        }
        $files = scandir($logFilePath);

        $logs = [];

        foreach ($files as $file) {
            $filePath = $logFilePath . '/' . $file;
            if (!is_file($filePath) || pathinfo($file, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $entryLines = [];

            foreach ($lines as $line) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                    if (!empty($entryLines)) {
                        $logs[] = self::parseLogEntry($entryLines, $file);
                        $entryLines = [];
                    }
                }
                $entryLines[] = $line;
            }

            if (!empty($entryLines)) {
                $logs[] = self::parseLogEntry($entryLines, $file);
            }
        }

        return array_filter($logs);
    }

    protected static function parseLogEntry(array $lines, string $file): ?array
    {
        $entry = implode("\n", $lines);

        preg_match('/\[(?<date>[\d\-:\s]+)\]\s(?<env>\w+)\.(?<level>\w+):\s(?<message>.*)/s', $entry, $matches);

        if (!isset($matches['level']) || !isset($matches['message'])) {
            return null;
        }

        return [
            'date' => trim($matches['date']),
            'env' => trim($matches['env']),
            'log_level' => LogLevel::from(strtolower(trim($matches['level']))),
            'message' => self::extractMessage($matches['message']),
            'stack' => self::extractStack($matches['message']),
            'file' => $file,
        ];
    }

    protected static function extractMessage(string $raw): string
    {
        return trim(preg_split('/\n|\{/', $raw, 2)[0]);
    }

    protected static function extractStack(string $raw): ?string
    {
        $stackTrace = app(Pipeline::class)
            ->send($raw)
            ->through([
                function ($raw, $next) {
                    return $next(explode("\n", $raw, 2));
                },
                function ($parts, $next) {
                    return $next(isset($parts[1]) ? trim($parts[1]) : null);
                },
                function ($emptyOrParts, $next) {
                    if (empty($emptyOrParts)) {
                        return null;
                    }
                    return $next($emptyOrParts);
                },
                function ($emptyOrParts, $next) {
                    return $next(explode("\n", $emptyOrParts));
                },
                function ($stackTraceArray, $next) {
                    return $next(array_slice($stackTraceArray, 1, -1));
                },
                function ($slicedTrace, $next) {
                    return $next(array_map(function ($item) {
                        return ['trace' => $item];
                    }, $slicedTrace));
                },
            ])
            ->thenReturn();
        return json_encode($stackTrace);
    }
}
