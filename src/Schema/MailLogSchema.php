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
        $placeholder = __('filament-log-viewer::log.placeholder');

        return $schema
            ->columns()
            ->components([
                Fieldset::make(__('filament-log-viewer::log.mail.sender.label'))
                    ->schema([
                        TextEntry::make('mail.sender.name')
                            ->label(__('filament-log-viewer::log.mail.sender.name'))
                            ->badge()
                            ->placeholder($placeholder),
                        TextEntry::make('mail.sender.email')
                            ->label(__('filament-log-viewer::log.mail.sender.email'))
                            ->badge()
                            ->placeholder($placeholder),
                    ]),
                Fieldset::make(__('filament-log-viewer::log.mail.receiver.label'))
                    ->schema([
                        TextEntry::make('mail.receiver.name')
                            ->label(__('filament-log-viewer::log.mail.receiver.name'))
                            ->badge()
                            ->placeholder($placeholder),
                        TextEntry::make('mail.receiver.email')
                            ->label(__('filament-log-viewer::log.mail.receiver.email'))
                            ->badge()
                            ->placeholder($placeholder),
                    ]),
                Tabs::make(__('filament-log-viewer::log.mail.content'))
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('filament-log-viewer::log.mail.plain'))
                            ->schema([
                                TextEntry::make('mail.plain')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->placeholder($placeholder),
                            ]),
                        Tab::make(__('filament-log-viewer::log.mail.html'))
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
