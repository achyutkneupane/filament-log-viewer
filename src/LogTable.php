<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Filters\FileFilter;
use AchyutN\FilamentLogViewer\Model\Log;
use AchyutN\FilamentLogViewer\Schema\ErrorLogSchema;
use AchyutN\FilamentLogViewer\Schema\JSONLogSchema;
use AchyutN\FilamentLogViewer\Schema\LogTableSchema;
use AchyutN\FilamentLogViewer\Schema\MailLogSchema;
use AchyutN\FilamentLogViewer\Traits\LogLevelTabFilter;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @phpstan-import-type LogRow from Log
 *
 * @phpstan-type LogCollection Collection<int|string, LogRow>
 * @phpstan-type FilterData array{date?: array{from?: string, until?: string}, file?: array{value: string}}
 */
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
    public static function getNavigationGroup(): string|UnitEnum|null
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
        return ltrim(
            self::getPlugin($panel)->getNavigationUrl(),
            '/'
        );
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

    public function getHeading(): string
    {
        return __('filament-log-viewer::log.navigation.heading');
    }

    public function getSubheading(): string
    {
        return __('filament-log-viewer::log.navigation.subheading');
    }

    public function getTitle(): string
    {
        return __('filament-log-viewer::log.navigation.title');
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->records(
                function (?array $filters, ?string $sortColumn, ?string $sortDirection, ?string $search, int $page, int $recordsPerPage): LengthAwarePaginator {
                    $records = Collection::wrap(Log::getRows());

                    $records = $this->applyTabFilter($records);
                    /** @var FilterData $filters */
                    $records = $this->applyDateFilter($records, $filters);
                    /** @var FilterData $filters */
                    $records = $this->applyFileFilter($records, $filters);
                    $records = $this->applySearchFilter($records, $search);

                    $records = filled($sortColumn)
                        ? $records->sortBy($sortColumn, SORT_DESC, $sortDirection === 'desc')
                        : $records->sortByDesc('date');

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
                    ->label(__('filament-log-viewer::log.table.actions.view.label'))
                    ->visible(
                        fn (array $record): bool => $record['log_level'] !== LogLevel::MAIL
                    )
                    ->hidden(
                        fn (array $record): bool => count((array) $record['stack']) === 0
                    )
                    ->icon(Heroicon::Eye)
                    ->color(Color::Gray)
                    ->schema(fn (Schema $schema): Schema => ErrorLogSchema::configure($schema))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading(
                        fn (array $record) => $record['message']
                    )
                    ->modalDescription(
                        /** @phpstan-param LogRow $record */
                        fn (array $record) => $record['description']
                    )
                    ->slideOver(),
                Action::make('view-json')
                    ->label(__('filament-log-viewer::log.table.actions.view.label'))
                    ->visible(fn (array $record): bool => $record['log_level'] !== LogLevel::MAIL)
                    ->hidden(fn (array $record): bool => $record['context'] === null)
                    ->icon(Heroicon::Eye)
                    ->color(Color::Gray)
                    ->schema(fn (Schema $schema): Schema => JSONLogSchema::configure($schema))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading(
                        /** @phpstan-param LogRow $record */
                        fn (array $record) => $record['message']
                    )
                    ->modalDescription(
                        /** @phpstan-param LogRow $record */
                        fn (array $record) => $record['description']
                    )
                    ->slideOver(),
                Action::make('read')
                    ->label(__('filament-log-viewer::log.table.actions.read.label'))
                    ->visible(fn (array $record): bool => $record['log_level'] === LogLevel::MAIL)
                    ->icon(Heroicon::Envelope)
                    ->color(Color::hex('#9C27B0'))
                    ->schema(fn (Schema $schema): Schema => MailLogSchema::configure($schema))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading(function (array $record): string {
                        /** @var LogRow $record */
                        $mail = $record['mail'];
                        if ($mail && isset($mail['subject']) && $mail['subject'] !== '') {
                            return __('filament-log-viewer::log.table.actions.read.subject').': '.$mail['subject'];
                        }

                        return __('filament-log-viewer::log.table.actions.read.mail_log');
                    })
                    ->modalDescription(function (array $record): ?string {
                        /** @var LogRow $record */
                        $mail = $record['mail'];
                        if ($mail && isset($mail['sent_date']) && $mail['sent_date'] !== '') {
                            return __('filament-log-viewer::log.table.actions.read.sent_date').': '.$mail['sent_date'];
                        }

                        return null;
                    })
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
            ->filtersFormColumns(3)
            ->deferFilters(false)
            ->deferColumnManager(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-log-viewer::log.table.actions.refresh.label'))
                ->icon(Heroicon::ArrowPath)
                ->outlined()
                ->action(function (): void {
                    $this->refresh();
                }),
            Action::make('clear')
                ->label(__('filament-log-viewer::log.table.actions.clear.label'))
                ->icon(Heroicon::Trash)
                ->color(Color::Red)
                ->visible(fn () => config('filament-log-viewer.enable_delete', true))
                ->requiresConfirmation()
                ->action(function (): void {
                    Log::destroyAllLogs();
                    Notification::make()
                        ->title(__('filament-log-viewer::log.table.actions.clear.success'))
                        ->success()
                        ->send();
                }),
        ];
    }

    /** @throws Exception */
    private static function getPlugin(?Panel $panel = null): FilamentLogViewer
    {
        $panel ??= Filament::getCurrentPanel();
        $logViewer = FilamentLogViewer::make();

        if ($panel?->hasPlugin($logViewer->getId())) {
            /** @var FilamentLogViewer */
            return $panel->getPlugin($logViewer->getId());
        }

        return $logViewer;
    }

    /**
     * @param  LogCollection  $records
     * @return LogCollection
     */
    private function applyTabFilter(Collection $records): Collection
    {
        if ($this->tableIsUnscoped()) {
            return $records;
        }

        return $records->filter(fn (array $log): bool => $log['log_level']->value === $this->activeTab);
    }

    /**
     * @param  LogCollection  $records
     * @param  FilterData|null  $filters
     * @return LogCollection
     */
    private function applyDateFilter(Collection $records, ?array $filters): Collection
    {
        if (empty($filters['date'])) {
            return $records;
        }

        return $records
            ->when(filled($filters['date']['from']), fn ($q) => $q->filter(
                fn (array $log): bool => $log['date'] >= $filters['date']['from']
            ))
            ->when(filled($filters['date']['until']), fn ($q) => $q->filter(
                fn (array $log): bool => $log['date'] <= $filters['date']['until']
            ));
    }

    /**
     * @param  LogCollection  $records
     * @param  FilterData|null  $filters
     * @return LogCollection
     */
    private function applyFileFilter(Collection $records, ?array $filters): Collection
    {
        if (! $filters || (array_key_exists('file', $filters) === false || blank($filters['file']['value']))) {
            return $records;
        }

        $file = mb_strtolower($filters['file']['value']);

        return $records->filter(fn (array $log): bool => mb_strtolower($log['file']) === $file);
    }

    /**
     * @param  LogCollection  $records
     * @return LogCollection
     */
    private function applySearchFilter(Collection $records, ?string $search): Collection
    {
        if (blank($search)) {
            return $records;
        }

        $needle = mb_strtolower($search);

        return $records->filter(fn (array $log): bool => str_contains(mb_strtolower($log['message']), $needle));
    }
}
