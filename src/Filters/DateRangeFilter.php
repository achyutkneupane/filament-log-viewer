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
                DatePicker::make('from')
                    ->label('From'),
                DatePicker::make('until')
                    ->label('Until'),
            ])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when(
                    $data['from'],
                    fn (Builder $query, $date): Builder => $query->whereDate($name, '>=', $date),
                )
                ->when(
                    $data['until'],
                    fn (Builder $query, $date): Builder => $query->whereDate($name, '<=', $date),
                ))
            ->indicateUsing(
                fn (array $data): array => self::indicators($data),
            );
    }

    private static function indicators(array $data): array
    {
        $indicators = [];

        if (! empty($data['from']) && ! empty($data['until'])) {
            $indicators[] = Indicator::make('Logs from '.Carbon::parse($data['from'])->toFormattedDateString().' to '.Carbon::parse($data['until'])->toFormattedDateString())
                ->removeField('from')
                ->removeField('until');
        } elseif (! empty($data['from'])) {
            $indicators[] = Indicator::make('Logs from '.Carbon::parse($data['from'])->toFormattedDateString())
                ->removeField('from');
        } elseif (! empty($data['until'])) {
            $indicators[] = Indicator::make('Logs until '.Carbon::parse($data['until'])->toFormattedDateString())
                ->removeField('until');
        }

        return $indicators;
    }
}
