<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Filters;

use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;

final class DateRangeFilter
{
    /** @throws Exception */
    public static function make(string $name = 'date_range'): Filter
    {
        return Filter::make($name)
            ->label('Date Range')
            ->indicator('Date Range')
            ->form([
                DatePicker::make('created_from')
                    ->label('From'),
                DatePicker::make('created_until')
                    ->label('Until'),
            ])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when(
                    $data['created_from'],
                    fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                )
                ->when(
                    $data['created_until'],
                    fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                ))
            ->indicateUsing(
                fn (array $data): array => self::indicators($data),
            );
    }

    private static function indicators(array $data): array
    {
        $indicators = [];

        if (isset($data['created_from'])) {
            $indicators[] = Indicator::make('Logs from '.Carbon::parse($data['created_from'])->toFormattedDateString())
                ->removeField('created_from');
        }

        if (isset($data['created_until'])) {
            $indicators[] = Indicator::make('Logs until '.Carbon::parse($data['created_until'])->toFormattedDateString())
                ->removeField('created_until');
        }

        return $indicators;
    }
}
