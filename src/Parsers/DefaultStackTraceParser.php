<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Parsers;

use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;

/**
 * @phpstan-type StackTrace array{trace: string}
 */
class DefaultStackTraceParser implements StackTraceParser
{
    /**
     * @return list<StackTrace>
     */
    public function extract(string $raw): array
    {
        $parts = explode("\n", $raw, 2);

        if (! isset($parts[1])) {
            return [];
        }

        $tracePart = mb_trim($parts[1]);
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
            $line = mb_trim($lines[$i]);
            if ($line !== '') {
                $result[] = ['trace' => $line];
            }
        }

        return $result;
    }

    public function hasStack(string $raw): bool
    {
        return str_contains($raw, '[stacktrace]') || str_contains($raw, '#0');
    }
}
