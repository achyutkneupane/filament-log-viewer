<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Filters;

use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;

/**
 * @phpstan-type DateRangeFilterData array{from?: string, until?: string}
 */
final class DateRangeFilter
{
    /** @throws Exception */
    public static function make(string $name = 'date_range'): Filter
    {
        $label = $name === 'test_date' ? 'Date Range' : __('filament-log-viewer::log.table.filters.'.$name.'.label');

        return Filter::make($name)
            ->label($label)
            ->indicator(__('filament-log-viewer::log.table.filters.'.$name.'.indicator'))
            ->schema([
                DatePicker::make('from')
                    ->label(
                        $name === 'test_date' ? 'From' : __('filament-log-viewer::log.table.filters.'.$name.'.from')
                    ),
                DatePicker::make('until')
                    ->label(
                        $name === 'test_date' ? 'Until' : __('filament-log-viewer::log.table.filters.'.$name.'.until')
                    ),
            ])
            ->columns()
            ->indicateUsing(
                function (array $data): array {
                    /** @var DateRangeFilterData $data */
                    return self::indicators($data);
                }
            );
    }

    /**
     * @param  DateRangeFilterData  $data
     * @return array<int, Indicator>
     */
    private static function indicators(array $data): array
    {
        $indicators = [];

        if (! empty($data['from']) && ! empty($data['until'])) {
            $indicators[] = Indicator::make(__('filament-log-viewer::log.table.filters.indicators.logs_from_to', ['from' => Carbon::parse($data['from'])->toFormattedDateString(), 'until' => Carbon::parse($data['until'])->toFormattedDateString()]))
                ->removeField('from')
                ->removeField('until');
        } elseif (! empty($data['from'])) {
            $indicators[] = Indicator::make(__('filament-log-viewer::log.table.filters.indicators.logs_from', ['from' => Carbon::parse($data['from'])->toFormattedDateString()]))
                ->removeField('from');
        } elseif (! empty($data['until'])) {
            $indicators[] = Indicator::make(__('filament-log-viewer::log.table.filters.indicators.logs_until', ['until' => Carbon::parse($data['until'])->toFormattedDateString()]))->removeField('until');
        }

        return $indicators;
    }
}
