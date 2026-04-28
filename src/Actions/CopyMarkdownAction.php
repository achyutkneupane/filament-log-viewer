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
            fn (?array $record): bool => $record && $record['log_level'] instanceof LogLevel && in_array(
                $record['log_level']->value,
                (array) config('filament-log-viewer.copy_markdown_levels', ['error'])
            )
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

        $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.message')."\n".$this->escapeMarkdown($record['message'])."\n\n";

        if (($record['description'] ?? '') !== '') {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.description')."\n`{$record['description']}`\n\n";
        }

        if (($record['context'] ?? null) !== null) {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.context')."\n```json\n".json_encode($record['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n```\n\n";
        }

        if ($record['has_stack'] && ($record['raw_stack'] ?? '') !== '') {
            $markdown .= '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.stack_trace')."\n```text\n{$record['raw_stack']}\n```\n\n";
        }

        if (($record['mail'] ?? null) !== null) {
            $markdown .= $this->generateMailSection($record['mail']);
        }

        return trim($markdown);
    }

    /** @param array{plain: string, html: string, sender: array{name: string, email: string}|null, receiver: array{name: string, email: string}|null, subject: string, sent_date: string} $mail */
    private function generateMailSection(array $mail): string
    {
        $section = '## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.mail')."\n\n";

        if (($mail['sender']['email'] ?? '') !== '') {
            $senderName = ($mail['sender']['name'] ?? '') ? $mail['sender']['name'].' <'.$mail['sender']['email'].'>' : $mail['sender']['email'];
            $section .= '- **From:** '.$senderName."\n";
        }

        if (($mail['receiver']['email'] ?? '') !== '') {
            $receiverName = ($mail['receiver']['name'] ?? '') ? $mail['receiver']['name'].' <'.$mail['receiver']['email'].'>' : $mail['receiver']['email'];
            $section .= '- **To:** '.$receiverName."\n";
        }

        if ($mail['subject'] !== '') {
            $section .= '- **Subject:** '.$this->escapeMarkdown($mail['subject'])."\n";
        }

        if ($mail['sent_date'] !== '') {
            $section .= '- **Date:** '.$mail['sent_date']."\n";
        }

        if ($mail['plain'] !== '') {
            $section .= "\n".'---'."\n\n".$mail['plain']."\n";
        }

        return $section."\n";
    }

    private function escapeMarkdown(string $text): string
    {
        return (string) preg_replace('/([*_`\[\]()#\\-])/', '\\\$1', $text);
    }
}
