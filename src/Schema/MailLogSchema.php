<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use Exception;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

final class MailLogSchema
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        $placeholder = 'N/A';

        return $schema
            ->columns()
            ->components([
                Fieldset::make('Sender')
                    ->schema([
                        TextEntry::make('mail.sender.name')
                            ->label('Name')
                            ->badge()
                            ->placeholder($placeholder),
                        TextEntry::make('mail.sender.email')
                            ->label('Email')
                            ->badge()
                            ->placeholder($placeholder),
                    ]),
                Fieldset::make('Receiver')
                    ->schema([
                        TextEntry::make('mail.receiver.name')
                            ->label('Name')
                            ->badge()
                            ->placeholder($placeholder),
                        TextEntry::make('mail.receiver.email')
                            ->label('Email')
                            ->badge()
                            ->placeholder($placeholder),
                    ]),
            ]);
    }
}
