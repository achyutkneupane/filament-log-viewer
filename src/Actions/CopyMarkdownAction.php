<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Js;

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

        $this->hidden(fn (): bool => ! $isEnabled);

        $this->visible(
            fn (?array $record): bool => $record && $record['log_level'] === LogLevel::ERROR
        );

        $this->alpineClickHandler(function (array $record): string {
            /** @phpstan-var LogRow $record */
            $copyState = Js::from($this->generateMarkdown($record));
            $successMessage = Js::from(__('filament-log-viewer::log.table.actions.copy_markdown.success'));

            return <<<JS
            const textarea = document.createElement('textarea');
            textarea.value = {$copyState};
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            new FilamentNotification()
                .success()
                .title({$successMessage})
                .send()
            JS;
        });
    }

    public static function getDefaultName(): string
    {
        return 'copy_markdown';
    }

    /** @param LogRow $record */
    private function generateMarkdown(array $record): string
    {
        $markdown = '# ['.$record['date'].'] '.$record['env'].'.'.$record['log_level']->value."\n\n";
        $markdown .= '**'.__('filament-log-viewer::log.table.actions.copy_markdown.headers.file').':** `'.$record['file']."`\n\n";

        $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.message')."\n".$record['message']."\n\n";

        if (($record['description'] ?? '') !== '') {
            $markdown .= '## '.__('filament-log-viewer::log.t  able.actions.copy_markdown.headers.description')."\n`{$record['description']}`\n\n";
        }

        if (($record['context'] ?? null) !== null) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.context')."\n```json\n".json_encode($record['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n```\n\n";
        }

        if ($record['has_stack'] && ($record['raw_stack'] ?? '') !== '') {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.stack_trace')."\n```text\n{$record['raw_stack']}\n```\n\n";
        }

        return trim($markdown);
    }
}
