<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use Exception;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;

final class LogTableSchema
{
    /**
     * @throws Exception
     */
    public static function columns(): array
    {
        return [
            TextColumn::make('log_level')
                ->badge(),
            TextColumn::make('env')
                ->label('Environment')
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
                ->label('File Name')
                ->badge()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('message')
                ->label('Summary')
                ->searchable()
                ->wrap(),
            TextColumn::make('date')
                ->label('Occurred')
                ->since()
                ->sortable()
                ->dateTimeTooltip(),
        ];
    }
}
