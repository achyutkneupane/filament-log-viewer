<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use AchyutN\FilamentLogViewer\Contracts\Schema\LogEntrySchemaInterface;
use Exception;
use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Schema;
use Phiki\Grammar\Grammar;

class JSONLogSchema implements LogEntrySchemaInterface
{
    /**
     * @throws Exception
     */
    public function configure(Schema $schema): Schema
    {
        return $schema
            ->key('json-log')
            ->components([
                CodeEntry::make('context')
                    ->grammar(Grammar::Json)
                    ->label(__('filament-log-viewer::log.schema.json-log.context')),
            ]);
    }
}
