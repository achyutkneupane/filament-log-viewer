<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Filters\FileFilter;
use AchyutN\FilamentLogViewer\Model\Log;
use AchyutN\FilamentLogViewer\Schema\ErrorLogSchema;
use AchyutN\FilamentLogViewer\Schema\LogTableSchema;
use AchyutN\FilamentLogViewer\Schema\MailLogSchema;
use AchyutN\FilamentLogViewer\Traits\LogLevelTabFilter;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
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
                            if (array_key_exists('stack', $log) && is_string($log['stack'])) {
                                $log['stack'] = json_decode($log['stack'], true);
                            }

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
                            filled($filters['file']['value']),
                            fn (Collection $data): Collection => $data->filter(
                                fn (array $log): bool => mb_strtolower((string) $log['file']) ===
                                    mb_strtolower((string) $filters['file']['value'] ?? '')
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
            ->columns(LogTableSchema::columns())
            ->recordActions([
                Action::make('view')
                    ->visible(fn (array $record): bool => $record['log_level'] !== LogLevel::MAIL)
                    ->icon(Heroicon::Eye)
                    ->color(Color::Gray)
                    ->schema(fn (Schema $schema): Schema => ErrorLogSchema::configure($schema))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading('Stack Trace')
                    ->modalDescription(fn (array $record): string => $record['message'])
                    ->slideOver(),
                Action::make('read')
                    ->visible(fn (array $record): bool => $record['log_level'] === LogLevel::MAIL)
                    ->icon(Heroicon::Envelope)
                    ->color(Color::hex('#9C27B0'))
                    ->schema(fn (Schema $schema): Schema => MailLogSchema::configure($schema))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading(fn (array $record): string => $record['mail']['subject'] ? 'Subject: '.$record['mail']['subject'] : 'Mail Log')
                    ->modalDescription(fn (array $record) => $record['mail']['sent_date'] ? 'Sent on: '.$record['mail']['sent_date'] : null)
                    ->slideOver(),
            ])
            ->poll(self::getPlugin()->getPollingTime())
            ->filters(
                [
                    DateRangeFilter::make('date')
                        ->columnSpan(2),
                    FileFilter::make()
                        ->columnSpan(1),
                ]
            )
            ->filtersFormWidth(Width::Large)
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
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
