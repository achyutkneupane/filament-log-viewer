<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Contracts\Schema;

use Filament\Tables\Columns\Column;

interface LogTableSchemaInterface
{
    /**
     * @return array<Column>
     */
    public function getColumns(): array;
}
