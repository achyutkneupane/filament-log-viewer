<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use AchyutN\FilamentLogViewer\Contracts\CanDeleteLogs;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\BasePage;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class ClearFileAction extends Action
{
    public string $file = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::Trash);

        $this->color(Color::Red);

        $this->visible(fn (): bool => (bool) config('filament-log-viewer.enable_delete', true) && app(LogProvider::class) instanceof CanDeleteLogs);

        $this->requiresConfirmation();

        $this->label(fn (): string => $this->file);

        $this->modalHeading(fn (): string => __('filament-log-viewer::log.table.actions.clear_file.modal_heading', ['file' => $this->file]));

        $this->modalDescription(fn (): string => __('filament-log-viewer::log.table.actions.clear_file.modal_description', ['file' => $this->file]));

        $this->action(function (): void {
            /** @var CanDeleteLogs $provider */
            $provider = app(LogProvider::class);
            $provider->deleteFile($this->file);
            Notification::make()
                ->title(__('filament-log-viewer::log.table.actions.clear_file.success', ['file' => $this->file]))
                ->success()
                ->send();
        });

        $this->after(function (): void {
            $livewire = $this->getLivewire();

            if ($livewire instanceof BasePage) {
                $livewire->refresh();
            }
        });
    }

    public static function getDefaultName(): string
    {
        return 'clear-file';
    }

    public function file(string $file): static
    {
        $this->file = $file;
        $this->name('clear-file-'.str_replace(['.', '/', '\\'], '-', $file));

        return $this;
    }
}
