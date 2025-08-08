<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Model\Log;
use AchyutN\FilamentLogViewer\Traits\LogLevelTabFilter;
use Exception;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class LogTable extends Page implements HasTable
{
    use InteractsWithTable;
    use LogLevelTabFilter;

    protected string $view = 'filament-log-viewer::log-table';

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
    public static function getSlug(?Panel $panel = null): string
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

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->records(
                function (?array $filters, ?string $sortColumn, ?string $sortDirection, ?string $search, int $page, int $recordsPerPage): LengthAwarePaginator {
                    $records = Collection::wrap(Log::getRows())
                        ->map(function (array $log): array {
                            $log['stack'] = json_decode($log['stack'] ?? []);

                            return $log;
                        })
                        ->when(
                            ! $this->tableIsUnscoped(),
                            fn (Collection $data): Collection => $data->where(
                                'log_level',
                                $this->activeTab
                            ),
                        )
                        ->when(
                            filled($filters['date']['from']),
                            fn (Collection $data): Collection => $data->where(
                                'date',
                                '>=',
                                $filters['date']['from']
                            )
                        )
                        ->when(
                            filled($filters['date']['until']),
                            fn (Collection $data): Collection => $data->where(
                                'date',
                                '<=',
                                $filters['date']['until']
                            )
                        )
                        ->when(
                            filled($sortColumn),
                            fn (Collection $data): Collection => $data->sortBy(
                                $sortColumn,
                                SORT_DESC,
                                $sortDirection === 'desc',
                            ),
                            fn (Collection $data): Collection => $data->sortByDesc(
                                'date'
                            )
                        )
                        ->when(
                            filled($search),
                            fn (Collection $data): Collection => $data->filter(
                                fn (array $log): bool => str_contains(
                                    mb_strtolower((string) $log['message']),
                                    mb_strtolower((string) $search)
                                )
                            )
                        );
                    $paginatedRecords = $records
                        ->forPage($page, $recordsPerPage);

                    return new LengthAwarePaginator(
                        $paginatedRecords,
                        total: count($records),
                        perPage: $recordsPerPage,
                        currentPage: $page,
                    );
                })
            ->columns([
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
            ])
            ->recordActions([
                Action::make('view')
                    ->icon(Heroicon::Eye)
                    ->color(Color::Gray)
                    ->schema([
                        RepeatableEntry::make('stack')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('trace')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ])
                            ->label('Stack Trace'),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading('Stack Trace')
                    ->modalDescription(fn (array $record): string => $record['message'])
                    ->slideOver(),
            ])
            ->poll(self::getPlugin()->getPollingTime())
            ->filters(
                [
                    DateRangeFilter::make('date'),
                ]
            )
            ->filtersFormWidth(Width::ExtraLarge)
            ->filtersFormColumns(1)
            ->deferFilters(false)
            ->deferColumnManager(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon(Heroicon::ArrowPath)
                ->outlined()
                ->action(function (): void {
                    $this->refresh();
                }),
            Action::make('clear')
                ->label('Clear Logs')
                ->icon(Heroicon::Trash)
                ->color(Color::Red)
                ->requiresConfirmation()
                ->action(function (): void {
                    Log::destroyAllLogs();
                    Notification::make()
                        ->title('Logs Cleared')
                        ->success()
                        ->send();
                }),
        ];
    }

    /** @throws Exception */
    private static function getPlugin(): FilamentLogViewer
    {
        return filament('filament-log-viewer');
    }
}
