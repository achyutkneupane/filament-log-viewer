<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts;

/**
 * Implement this interface to allow log deletion from the viewer.
 *
 * Providers that only read logs (e.g. a Pail tail or a read-only cloud
 * reader) should omit this interface so the clear actions stay hidden.
 */
interface CanDeleteLogs
{
    public function deleteAll(): void;

    public function deleteFile(string $file): void;
}
