<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Model\Log;
use Exception;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

final class LogTable extends Page implements HasTable
{
    use InteractsWithTable;

    protected FilamentLogViewer $plugin;

    protected static ?string $title = 'Logss';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament-log-viewer::log-table';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->plugin = $this->getPlugin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Log::query())
            ->columns([
                Tables\Columns\TextColumn::make('log_level')
                    ->badge(),
                Tables\Columns\TextColumn::make('env')
                    ->label('Environment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file')
                    ->label('File Name')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Summary'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Occurred')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->filters([
                //
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

    /**
     * @throws Exception
     */
    protected function getPlugin(): FilamentLogViewer
    {
        return Filament::getCurrentPanel()->getPlugin('filament-log-viewer');
    }
}
