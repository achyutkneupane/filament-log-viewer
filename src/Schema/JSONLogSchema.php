<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use Exception;
use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Schema;

final class JSONLogSchema
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->key('json-log')
            ->components([
                CodeEntry::make('context'),
            ]);
    }
}
