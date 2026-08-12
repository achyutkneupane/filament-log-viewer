<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Actions\CopyMarkdownAction;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\Schema\LogEntrySchemaInterface;
use AchyutN\FilamentLogViewer\Contracts\Schema\LogTableSchemaInterface;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Filters\FileFilter;
use AchyutN\FilamentLogViewer\Traits\HasLogViewerNavigation;
use AchyutN\FilamentLogViewer\Traits\LogLevelTabFilter;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @phpstan-import-type LogRow from LogProvider
 *
 * @phpstan-type LogCollection Collection<int|string, LogRow>
 * @phpstan-type FilterData array{date?: array{from?: string, until?: string}, file?: array{value: string}}
 */
class LogTable extends Page implements HasTable
{
    use HasLogViewerNavigation;
    use InteractsWithTable;
    use LogLevelTabFilter;

    protected string $view = 'filament-log-viewer::log-table';

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        $plugin = self::getPlugin();

        /** @var LogProvider $provider */
        $provider = app($plugin->getProviderClass());

        /** @var LogTableSchemaInterface $tableSchema */
        $tableSchema = app($plugin->getTableSchemaClass());

        /** @var LogEntrySchemaInterface $errorSchema */
        $errorSchema = app($plugin->getErrorSchemaClass());

        /** @var LogEntrySchemaInterface $jsonSchema */
        $jsonSchema = app($plugin->getJsonSchemaClass());

        /** @var LogEntrySchemaInterface $mailSchema */
        $mailSchema = app($plugin->getMailSchemaClass());

        /** @var class-string<CopyMarkdownAction> $copyMarkdownClass */
        $copyMarkdownClass = $plugin->getCopyMarkdownActionClass();

        return $table
            ->modelLabel(__('filament-log-viewer::log.table.model_label'))
            ->pluralModelLabel(__('filament-log-viewer::log.table.plural_model_label'))
            ->records(
                function (?array $filters, ?string $sortColumn, ?string $sortDirection, ?string $search, int $page, int $recordsPerPage) use ($plugin): LengthAwarePaginator {
                    /** @var LogProvider $provider */
                    $provider = app($plugin->getProviderClass());

                    $records = Collection::wrap($provider->getRows());

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
            ->columns($tableSchema->getColumns())
            ->recordActions([
                $this->getViewAction($errorSchema),
                $this->getViewJsonAction($jsonSchema),
                $this->getReadMailAction($mailSchema),
                $copyMarkdownClass::make(),
            ])
            ->poll($plugin->getPollingTime())
            ->filters(
                [
                    $this->getDateRangeFilter(),
                    $this->getFileFilter(),
                ]
            )
            ->filtersFormWidth(Width::Large)
            ->filtersFormColumns(3)
            ->deferFilters(false)
            ->deferColumnManager(false);
    }

    protected function getViewAction(LogEntrySchemaInterface $entrySchema): Action
    {
        return Action::make('view')
            ->label(__('filament-log-viewer::log.table.actions.view.label'))
            ->visible(
                fn (array $record): bool => $record['log_level'] !== LogLevel::MAIL
            )
            ->hidden(fn (array $record): bool => ! $record['has_stack'])
            ->icon(Heroicon::Eye)
            ->color(Color::Gray)
            ->schema(fn (Schema $schema): Schema => $entrySchema->configure($schema))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalHeading(
                fn (array $record) => $record['message']
            )
            ->modalDescription(
                /** @phpstan-param LogRow $record */
                fn (array $record) => $record['description']
            )
            ->slideOver();
    }

    protected function getViewJsonAction(LogEntrySchemaInterface $entrySchema): Action
    {
        return Action::make('view-json')
            ->label(__('filament-log-viewer::log.table.actions.view.label'))
            ->visible(fn (array $record): bool => $record['log_level'] !== LogLevel::MAIL)
            ->hidden(fn (array $record): bool => $record['context'] === null)
            ->icon(Heroicon::Eye)
            ->color(Color::Gray)
            ->schema(fn (Schema $schema): Schema => $entrySchema->configure($schema))
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
            ->slideOver();
    }

    protected function getReadMailAction(LogEntrySchemaInterface $entrySchema): Action
    {
        return Action::make('read')
            ->label(__('filament-log-viewer::log.table.actions.read.label'))
            ->visible(fn (array $record): bool => $record['log_level'] === LogLevel::MAIL)
            ->icon(Heroicon::Envelope)
            ->color(Color::hex('#9C27B0'))
            ->schema(fn (Schema $schema): Schema => $entrySchema->configure($schema))
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
            ->slideOver();
    }

    protected function getDateRangeFilter(): \Filament\Tables\Filters\Filter
    {
        $plugin = self::getPlugin();

        /** @var class-string<DateRangeFilter> $class */
        $class = $plugin->getDateRangeFilterClass();

        return $class::make('date')->columnSpan(2);
    }

    protected function getFileFilter(): \Filament\Tables\Filters\SelectFilter
    {
        $plugin = self::getPlugin();

        /** @var class-string<FileFilter> $class */
        $class = $plugin->getFileFilterClass();

        return $class::make()->columnSpan(1);
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
                    /** @var LogProvider $provider */
                    $provider = app(LogProvider::class);
                    $provider->deleteAll();
                    Notification::make()
                        ->title(__('filament-log-viewer::log.table.actions.clear.success'))
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * @param  LogCollection  $records
     * @return LogCollection
     */
    protected function applyTabFilter(Collection $records): Collection
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
    protected function applyDateFilter(Collection $records, ?array $filters): Collection
    {
        if (empty($filters['date'])) {
            return $records;
        }

        $from = $filters['date']['from'] ?? null;
        $until = $filters['date']['until'] ?? null;

        if (filled($from)) {
            $records = $records->filter(
                fn (array $log): bool => $log['date'] >= $from
            );
        }

        if (filled($until)) {
            return $records->filter(
                fn (array $log): bool => $log['date'] <= $until
            );
        }

        return $records;
    }

    /**
     * @param  LogCollection  $records
     * @param  FilterData|null  $filters
     * @return LogCollection
     */
    protected function applyFileFilter(Collection $records, ?array $filters): Collection
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
    protected function applySearchFilter(Collection $records, ?string $search): Collection
    {
        if (blank($search)) {
            return $records;
        }

        $needle = mb_strtolower($search);

        return $records->filter(fn (array $log): bool => str_contains(mb_strtolower($log['message']), $needle));
    }
}
