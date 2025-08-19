<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Filters;

use AchyutN\FilamentLogViewer\Model\Log;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
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
