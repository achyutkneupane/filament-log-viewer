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
                    ->state(
                        function (array $record): array {
                            /** @var string $rawStack */
                            $rawStack = $record['raw_stack'];

                            return Log::getStackFromRaw($rawStack);
                        }
                    )
                    ->schema([
                        TextEntry::make('trace')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->label(__('filament-log-viewer::log.schema.error-log.stack')),
            ]);
    }
}
