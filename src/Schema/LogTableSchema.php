<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use AchyutN\FilamentLogViewer\Contracts\Schema\LogTableSchemaInterface;
use Exception;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class LogTableSchema implements LogTableSchemaInterface
{
    /**
     * @return array<Column>
     *
     * @throws Exception
     */
    public function getColumns(): array
    {
        return [
            TextColumn::make('log_level')
                ->label(__('filament-log-viewer::log.table.columns.log_level'))
                ->badge(),
            TextColumn::make('env')
                ->label(__('filament-log-viewer::log.table.columns.env'))
                ->color(fn (string $state): array => match ($state) {
                    'local' => Color::Blue,
                    'production' => Color::Red,
                    'staging' => Color::Orange,
                    'testing' => Color::Gray,
                    default => Color::Yellow
                })
                ->toggleable(isToggledHiddenByDefault: true)
                ->badge(),
            TextColumn::make('file')
                ->label(__('filament-log-viewer::log.table.columns.file'))
                ->badge()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('message')
                ->label(__('filament-log-viewer::log.table.columns.message'))
                ->searchable()
                ->wrap(),
            TextColumn::make('date')
                ->label(__('filament-log-viewer::log.table.columns.date'))
                ->since()
                ->sortable()
                ->dateTimeTooltip(),
        ];
    }
}
