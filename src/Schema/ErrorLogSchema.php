<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\Schema\LogEntrySchemaInterface;
use Exception;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ErrorLogSchema implements LogEntrySchemaInterface
{
    /**
     * @throws Exception
     */
    public function configure(Schema $schema): Schema
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

                            /** @var LogProvider $provider */
                            $provider = app(LogProvider::class);

                            return $provider->getStackFromRaw($rawStack);
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
