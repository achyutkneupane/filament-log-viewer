<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Filters;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use Exception;
use Filament\Tables\Filters\SelectFilter;

class FileFilter
{
    /** @throws Exception */
    public static function make(string $name = 'file'): SelectFilter
    {
        /** @var LogProvider $provider */
        $provider = app(LogProvider::class);

        return SelectFilter::make($name)
            ->label($name === 'test_file' ? 'File' : __('filament-log-viewer::log.table.filters.'.$name.'.label'))
            ->options($provider->getFilesForFilter())
            ->indicator('File');
    }
}
