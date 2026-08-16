<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature\Stubs;

use AchyutN\FilamentLogViewer\Parsers\FileLogParser;
use Generator;

/**
 * Route B: extend the default parser and decorate its output.
 */
final class ScopedLogParser extends FileLogParser
{
    public function parse(string $filePath, string $fileName): Generator
    {
        foreach (parent::parse($filePath, $fileName) as $row) {
            $row['message'] .= ' [scoped]';

            yield $row;
        }
    }
}
