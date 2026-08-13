<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts;

/**
 * @phpstan-type StackTrace array{trace: string}
 */
interface StackTraceParser
{
    /**
     * @return list<StackTrace>
     */
    public function extract(string $rawStack): array;

    public function hasStack(string $raw): bool;
}
