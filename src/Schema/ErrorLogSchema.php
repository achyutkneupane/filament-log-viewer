<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use AchyutN\FilamentLogViewer\Model\Log;
use Exception;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class ErrorLogSchema
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->key('error-log')
            ->schema([
                RepeatableEntry::make('stack')
                    ->hiddenLabel()
                    ->state(fn (array $record): array => Log::getStackFromRaw($record['raw_stack'] ?? '') ?? [])
                    ->schema([
                        TextEntry::make('trace')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->label(__('filament-log-viewer::log.schema.error-log.stack')),
            ]);
    }
}
