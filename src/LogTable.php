<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Model\Log;
use Exception;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

final class LogTable extends Page implements HasTable
{
    use InteractsWithTable;

    #[Url(except: null)]
    public ?string $activeTab = null;

    protected static string $view = 'filament-log-viewer::log-table';

    /**
     * @var array<string | int, Tab>
     */
    private array $cachedTabs;

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

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Log::query()
            )
            ->modifyQueryUsing(function (Builder $query): void {
                if ($this->activeTab) {
                    $query->where('log_level', $this->activeTab);
                }
            })
            ->headerActions([
                Tables\Actions\Action::make('clear')
                    ->label('Clear Logs')
                    ->icon('heroicon-o-trash')
                    ->color(Color::Red)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        Log::destroyAllLogs();
                        Notification::make()
                            ->title('Logs Cleared')
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('log_level')
                    ->badge(),
                Tables\Columns\TextColumn::make('env')
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
            ->filters([
                DateRangeFilter::make('date'),
            ])
            ->defaultSort('date', 'desc');
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getCachedTabs(): array
    {
        return $this->cachedTabs ??= $this->getTabs();
    }

    /** @return array<string, mixed> */
    public function getTabs(): array
    {
        $all_logs = [
            null => Tab::make('All Logs')
                ->badge(fn () => Log::query()->count() ?: null),
        ];

        $tabs = collect(LogLevel::cases())
            ->mapWithKeys(fn (LogLevel $level) => [
                $level->value => Tab::make($level->getLabel())
                    ->badge(
                        fn () => Log::query()->where('log_level', $level)->count() ?: null
                    )
                    ->badgeColor($level->getColor()),
            ])->toArray();

        return array_merge($all_logs, $tabs);
    }

    public function getDefaultActiveTab(): null
    {
        return null;
    }

    public function updateTab(?LogLevel $level): void
    {
        $this->activeTab = $level?->value;
    }

    /** @throws Exception */
    private static function getPlugin(): FilamentLogViewer
    {
        $panel = Filament::getCurrentPanel();

        return $panel?->getPlugin('filament-log-viewer');
    }
}
