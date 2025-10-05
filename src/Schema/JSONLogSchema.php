<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use Exception;
use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Schema;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

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
                CodeEntry::make('context')
                    ->copyable()
                    ->copyMessage('Copied to Clipboard')
                    ->lightTheme(Theme::CatppuccinMocha)
                    ->darkTheme(Theme::CatppuccinMocha)
                    ->grammar(Grammar::Json),
            ]);
    }
}
