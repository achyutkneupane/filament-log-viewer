<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Exception;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

final class LogTable extends Page implements HasTable
{
    use HasTabs, InteractsWithTable;

    protected static string $view = 'filament-log-viewer::log-table';

    public function table(Table $table): Table
    {
        return $table
            ->query(Log::query())
            ->columns([
                Tables\Columns\TextColumn::make('log_level')
                    ->badge(),
                Tables\Columns\TextColumn::make('env')
                    ->label('Environment')
                    ->color(fn (string $state) => match ($state) {
                        'local' => Color::Blue,
                        'production' => Color::Red,
                        'staging' => Color::Orange,
                        'testing' => Color::Gray,
                        default => Color::Yellow
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                Tables\Columns\TextColumn::make('file')
                    ->label('File Name')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->label('Summary')
                    ->wrap(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Occurred')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('view')
                    ->infolist([
                        RepeatableEntry::make('stack')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('trace')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ])
                            ->label('Stack Trace'),
                    ])
                    ->slideOver(),
            ])
            ->defaultSort('date', 'desc');
    }

    public function getTabs(): array
    {
        $all_logs = [
            null => Tab::make('All Logs')
                ->badge(fn () => Log::query()->count() ?: null),
        ];

        $tabs = collect(LogLevel::cases())
            ->mapWithKeys(function (LogLevel $level) {
                return [
                    $level->value => Tab::make($level->getLabel())
                        ->badge(
                            fn () => Log::query()->where('log_level', $level)->count() ?: null
                        )
                        ->badgeColor($level->getColor())
                        ->modifyQueryUsing(function ($query) use ($level) {
                            return $query->where('log_level', $level);
                        }),
                ];
            })->toArray();

        return array_merge($all_logs, $tabs);
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return null;
    }

    /** @throws Exception */
    private static function getPlugin(): FilamentLogViewer
    {
        return Filament::getCurrentPanel()->getPlugin('filament-log-viewer');
    }

    /** @throws Exception */
    public static function getNavigationLabel(): string
    {
        return self::getPlugin()->getNavigationLabel();
    }

    /** @throws Exception */
    public static function getNavigationGroup(): string
    {
        return self::getPlugin()->getNavigationGroup();
    }

    /** @throws Exception */
    public static function getNavigationSort(): int
    {
        return self::getPlugin()->getNavigationSort();
    }

    /** @throws Exception */
    public static function getSlug(): string
    {
        return self::getPlugin()->getNavigationUrl();
    }

    /** @throws Exception */
    public static function getNavigationIcon(): string
    {
        return self::getPlugin()->getNavigationIcon();
    }

    /** @throws Exception */
    public static function canAccess(): bool
    {
        return self::getPlugin()->isAuthorized();
    }
}
