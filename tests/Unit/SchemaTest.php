<?php

declare(strict_types=1);

use Filament\Support\Colors\Color;

it('renders LogTableSchema', function () {
    $columns = (new AchyutN\FilamentLogViewer\Schema\LogTableSchema())->getColumns();

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

it('renders ErrorLogSchema', function () {
    $schema = (new AchyutN\FilamentLogViewer\Schema\ErrorLogSchema())->configure(new Filament\Schemas\Schema());
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

it('renders MailLogSchema', function () {
    $schema = (new AchyutN\FilamentLogViewer\Schema\MailLogSchema())->configure(new Filament\Schemas\Schema());
    expect($schema)->toBeInstanceOf(Filament\Schemas\Schema::class);

    expect($schema->getComponents())->toHaveCount(3);

    $fieldsetSender = $schema->getComponents()[0];
    expect($fieldsetSender)->toBeInstanceOf(Filament\Schemas\Components\Fieldset::class);
    expect($fieldsetSender->getLabel())->toBe('Sender');
    expect($fieldsetSender->getDefaultChildComponents())
        ->toHaveCount(2);

    $fieldSetReceiver = $schema->getComponents()[1];
    expect($fieldSetReceiver)->toBeInstanceOf(Filament\Schemas\Components\Fieldset::class);
    expect($fieldSetReceiver->getLabel())->toBe('Receiver');
    expect($fieldSetReceiver->getDefaultChildComponents())
        ->toHaveCount(2);

    $tabs = $schema->getComponents()[2];
    expect($tabs)->toBeInstanceOf(Filament\Schemas\Components\Tabs::class);
    expect($tabs->getLabel())->toBe('Content');
    expect($tabs->getDefaultChildComponents())->toHaveCount(2);

    $plainTextTab = $tabs->getDefaultChildComponents()[0];
    expect($plainTextTab)->toBeInstanceOf(Filament\Schemas\Components\Tabs\Tab::class);
    expect($plainTextTab->getLabel())->toBe('Plain Text');
    expect($plainTextTab->getDefaultChildComponents())->toHaveCount(1);

    $plainTextEntry = $plainTextTab->getDefaultChildComponents()[0];
    expect($plainTextEntry)->toBeInstanceOf(Filament\Infolists\Components\TextEntry::class);
    expect($plainTextEntry->getLabel())->toBe('Plain');
    expect($plainTextEntry->isLabelHidden())->toBeTrue();
    expect($plainTextEntry->isMarkdown())->toBeTrue();

    $htmlTab = $tabs->getDefaultChildComponents()[1];
    expect($htmlTab)->toBeInstanceOf(Filament\Schemas\Components\Tabs\Tab::class);
    expect($htmlTab->getLabel())->toBe('HTML');
    expect($htmlTab->getDefaultChildComponents())->toHaveCount(1);

    $htmlEntry = $htmlTab->getDefaultChildComponents()[0];
    expect($htmlEntry)->toBeInstanceOf(Filament\Infolists\Components\TextEntry::class);
    expect($htmlEntry->getLabel())->toBe('Html');
    expect($htmlEntry->isLabelHidden())->toBeTrue();
    expect($htmlEntry->isHtml())->toBeTrue();
});
