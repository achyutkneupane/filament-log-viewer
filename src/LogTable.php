<?php

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class LogTable extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = "Logs";

    protected static ?string $navigationIcon = "heroicon-o-document-text";

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
                    ->label("Occurred")
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
                            ->label('Stack Trace')
                    ])
                    ->slideOver()
            ])
            ->defaultSort('date', 'desc');
    }
}
