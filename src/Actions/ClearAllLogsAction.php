<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class ClearAllLogsAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-log-viewer::log.table.actions.clear.label'));

        $this->icon(Heroicon::Trash);

        $this->color(Color::Red);

        $this->visible(fn (): bool => (bool) config('filament-log-viewer.enable_delete', true));

        $this->requiresConfirmation();

        $this->action(function (): void {
            /** @var LogProvider $provider */
            $provider = app(LogProvider::class);
            $provider->deleteAll();
            Notification::make()
                ->title(__('filament-log-viewer::log.table.actions.clear.success'))
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): string
    {
        return 'clear';
    }
}
