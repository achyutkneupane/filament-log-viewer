<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Livewire\Component;

class CopyMarkdownAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'copy_markdown';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-log-viewer::log.table.actions.copy_markdown.label'))
            ->icon(Heroicon::DocumentDuplicate)
            ->color(Color::Gray)
            ->action(function (array $record, Component $livewire): void {
                $markdown = $this->generateMarkdown($record);

                // Safely encode the markdown string for JS execution to prevent syntax errors on massive stack traces
                $livewire->js('window.navigator.clipboard.writeText('.json_encode($markdown).');');

                Notification::make()
                    ->title(__('filament-log-viewer::log.table.actions.copy_markdown.success'))
                    ->success()
                    ->send();
            });
    }

    protected function generateMarkdown(array $record): string
    {
        $markdown = "# [{$record['date']}] {$record['env']}.{$record['log_level']->name}\n\n";
        $markdown .= '**'.__('filament-log-viewer::log.table.actions.copy_markdown.headers.file').":** `{$record['file']}`\n\n";

        $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.message')."\n{$record['message']}\n\n";

        if (! empty($record['description'])) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.description')."\n`{$record['description']}`\n\n";
        }

        if (! empty($record['context'])) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.context')."\n```json\n".json_encode($record['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n```\n\n";
        }

        if ($record['has_stack'] && ! empty($record['raw_stack'])) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.stack_trace')."\n```text\n{$record['raw_stack']}\n```\n\n";
        }

        if (! empty($record['mail'])) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.mail_details')."\n```json\n".json_encode($record['mail'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n```\n\n";
        }

        return trim($markdown);
    }
}
