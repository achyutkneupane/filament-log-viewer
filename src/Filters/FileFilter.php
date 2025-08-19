<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Filters;

use AchyutN\FilamentLogViewer\Model\Log;
use Exception;
use Filament\Tables\Filters\SelectFilter;

final class FileFilter
{
    /** @throws Exception */
    public static function make(string $name = 'file'): SelectFilter
    {
        return SelectFilter::make($name)
            ->label('File')
            ->options(Log::getFilesForFilter())
            ->indicator('File');
    }
}
