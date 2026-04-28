<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

/** @phpstan-import-type LogRow from Log */
final class CopyMarkdownAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $isEnabled = config()->boolean('filament-log-viewer.enable_copy_markdown', true);

        $this->label(__('filament-log-viewer::log.table.actions.copy_markdown.label'));

        $this->icon(Heroicon::DocumentDuplicate);

        $this->color(Color::Gray);

        $this->visible(fn (array $record): bool => $isEnabled && $record['log_level'] !== LogLevel::MAIL);

        $this->action(
            function (array $record, Component $livewire): void {
                /** @var LogRow $record */
                $markdown = $this->generateMarkdown($record);

                // Safely encode the markdown string for JS execution to prevent syntax errors on massive stack traces
                $livewire->js('window.navigator.clipboard.writeText('.json_encode($markdown).');');

                Notification::make()
                    ->title(__('filament-log-viewer::log.table.actions.copy_markdown.success'))
                    ->success()
                    ->send();
            }
        );
    }

    public static function getDefaultName(): string
    {
        return 'copy_markdown';
    }

    /** @param LogRow $record */
    private function generateMarkdown(array $record): string
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

        return trim($markdown);
    }
}
