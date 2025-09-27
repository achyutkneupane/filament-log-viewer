<?php

declare(strict_types=1);

use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;

it('renders DateRangeFilter', function () {
    $filters = DateRangeFilter::make();

    expect($filters)->toBeInstanceOf(Filament\Tables\Filters\Filter::class);
    expect($filters->getName())->toBe('date_range');
    expect($filters->getLabel())->toBe('Date Range');
    expect($filters->getColumns())->toBe(2);
    expect($filters->getSchemaComponents())->toHaveCount(2);

    expect($filters->getSchemaComponents())
        ->sequence(
            fn ($filter) => $filter->getName() === 'from' && $filter->getLabel() === 'From',
            fn ($filter) => $filter->getName() === 'until' && $filter->getLabel() === 'Until',
        );
});
