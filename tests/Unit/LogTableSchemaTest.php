<?php

declare(strict_types=1);

use Filament\Support\Colors\Color;

it('renders LogTableSchema', function () {
    $columns = AchyutN\FilamentLogViewer\Schema\LogTableSchema::columns();

    expect($columns)->toBeArray();
    expect($columns)->toHaveCount(5);

    $logLevelColumn = $columns[0];
    expect($logLevelColumn)->toBeInstanceOf(Filament\Tables\Columns\TextColumn::class);
    expect($logLevelColumn->getName())->toBe('log_level');
    expect($logLevelColumn->isBadge())->toBeTrue();

    $envColumn = $columns[1];
    expect($envColumn)->toBeInstanceOf(Filament\Tables\Columns\TextColumn::class);
    expect($envColumn->getName())->toBe('env');
    expect($envColumn->getLabel())->toBe('Environment');
    expect($envColumn->isToggleable())->toBeTrue();
    expect($envColumn->isBadge())->toBeTrue();
    expect($envColumn->getColor('local'))->toBe(Color::Blue);
    expect($envColumn->getColor('production'))->toBe(Color::Red);
    expect($envColumn->getColor('staging'))->toBe(Color::Orange);
    expect($envColumn->getColor('testing'))->toBe(Color::Gray);
    expect($envColumn->getColor('default'))->toBe(Color::Yellow);

    $fileColumn = $columns[2];
    expect($fileColumn)->toBeInstanceOf(Filament\Tables\Columns\TextColumn::class);
    expect($fileColumn->getName())->toBe('file');
    expect($fileColumn->getLabel())->toBe('File Name');
    expect($fileColumn->isBadge())->toBeTrue();
    expect($fileColumn->isToggleable())->toBeTrue();

    $messageColumn = $columns[3];
    expect($messageColumn)->toBeInstanceOf(Filament\Tables\Columns\TextColumn::class);
    expect($messageColumn->getName())->toBe('message');
    expect($messageColumn->getLabel())->toBe('Summary');
    expect($messageColumn->isSearchable())->toBeTrue();
    expect($messageColumn->canWrap())->toBeTrue();

    $dateColumn = $columns[4];
    expect($dateColumn)->toBeInstanceOf(Filament\Tables\Columns\TextColumn::class);
    expect($dateColumn->getName())->toBe('date');
    expect($dateColumn->getLabel())->toBe('Occurred');
    expect($dateColumn->isSortable())->toBeTrue();
});
