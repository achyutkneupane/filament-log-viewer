<?php

declare(strict_types=1);

it('renders ErrorLogSchema', function () {
    $schema = AchyutN\FilamentLogViewer\Schema\ErrorLogSchema::configure(new Filament\Schemas\Schema());
    expect($schema)->toBeInstanceOf(Filament\Schemas\Schema::class);
    expect($schema->getKey())->toBe('error-log');
    expect($schema->getComponents())->toHaveCount(1);

    $repeatableEntry = $schema->getComponents()[0];
    expect($repeatableEntry)->toBeInstanceOf(Filament\Infolists\Components\RepeatableEntry::class);
    expect($repeatableEntry->getName())->toBe('stack');
    expect($repeatableEntry->getLabel())->toBe('Stack Trace');
    expect($repeatableEntry->isLabelHidden())->toBeTrue();

    $textEntry = $repeatableEntry->getDefaultChildComponents()[0];
    expect($textEntry)->toBeInstanceOf(Filament\Infolists\Components\TextEntry::class);
    expect($textEntry->getName())->toBe('trace');
    expect($textEntry->getLabel())->toBe('Trace');
    expect($textEntry->isLabelHidden())->toBeTrue();
    expect($textEntry->getDefaultChildComponents())->toHaveCount(0);
});
