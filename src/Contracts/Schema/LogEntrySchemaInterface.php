<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts\Schema;

use Filament\Schemas\Schema;

interface LogEntrySchemaInterface
{
    public function configure(Schema $schema): Schema;
}
