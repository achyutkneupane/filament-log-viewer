<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Schema;

use Exception;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                Tabs::make('Content')
                    ->columnSpanFull()
                    ->default('HTML')
                    ->tabs([
                        Tab::make('Plain Text')
                            ->schema([
                                TextEntry::make('mail.plain')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->placeholder($placeholder),
                            ]),
                        Tab::make('HTML')
                            ->schema([
                                TextEntry::make('mail.html')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->html()
                                    ->placeholder($placeholder),
                            ]),
                    ]),
            ]);
    }
}
