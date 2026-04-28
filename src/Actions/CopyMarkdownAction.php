<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Actions;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Js;
use Throwable;

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
        $markdown = '# '.ucwords($record['log_level']->value)."\n\n";

        $markdown .= '**Date:** '.$this->formatDate($record['date'])."\n";
        $markdown .= '**Environment:** '.$record['env']."\n";
        $markdown .= '**File:** '.$record['file']."\n\n";

        if ($record['log_level'] !== LogLevel::MAIL) {
            $markdown .= '## Message'."\n".$this->escapeMarkdown($record['message'])."\n\n";
        }

        if (($record['description'] ?? '') !== '') {
            $markdown .= '## Description'."\n".$record['description']."\n\n";
        }

        if (($record['context'] ?? null) !== null) {
            $markdown .= '## Context'."\n".'```json'."\n".json_encode($record['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n```\n\n";
        }

        if ($record['has_stack'] && ($record['raw_stack'] ?? '') !== '') {
            $markdown .= '## Stack Trace'."\n";
            $frames = $this->parseStackTrace($record['raw_stack']);
            foreach ($frames as $index => $frame) {
                $markdown .= $index.' - '.$frame['file'].':'.$frame['line']."\n";
            }
            $markdown .= "\n";
        }

        if (($record['mail'] ?? null) !== null) {
            $markdown .= $this->generateMailSection($record['mail']);
        }

        return trim($markdown);
    }

    /** @return list<array{file: string, line: string}> */
    private function parseStackTrace(string $rawStack): array
    {
        $frames = [];
        $lines = explode("\n", $rawStack);

        $inStackTrace = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '[stacktrace]') {
                $inStackTrace = true;

                continue;
            }

            if ($inStackTrace && $line !== '' && $line !== '""') {
                if (preg_match('/^#(\d+)\s+(.+?)<?:\s*(\d+)>?$/', $line, $matches)) {
                    $filePath = $matches[2];
                    $lineNum = $matches[3];
                } elseif (preg_match('/^(.+?)\((\d+)\)/', $line, $matches)) {
                    $filePath = $matches[1];
                    $lineNum = $matches[2];
                } else {
                    continue;
                }

                $frames[] = [
                    'file' => $filePath,
                    'line' => $lineNum,
                ];
            }
        }

        return $frames;
    }

    /** @param array{plain: string, html: string, sender: array{name: string, email: string}|null, receiver: array{name: string, email: string}|null, subject: string, sent_date: string} $mail */
    private function generateMailSection(array $mail): string
    {
        $fromEmail = $mail['sender']['email'] ?? '';
        $fromName = $mail['sender']['name'] ?? '';
        $toEmail = $mail['receiver']['email'] ?? '';
        $toName = $mail['receiver']['name'] ?? '';

        $markdown = '**Sent Date:** '.$this->formatDate($mail['sent_date'] ?? '')."\n";

        if ($fromName !== '' || $fromEmail !== '') {
            $markdown .= '**From:** '.($fromName !== '' ? $fromName.' <'.$fromEmail.'>' : $fromEmail)."\n";
        }

        if ($toName !== '' || $toEmail !== '') {
            $markdown .= '**To:** '.($toName !== '' ? $toName.' <'.$toEmail.'>' : $toEmail)."\n";
        }

        if ($mail['subject'] !== '') {
            $markdown .= '**Subject:** '.$mail['subject']."\n";
        }

        $markdown .= "\n";

        $markdown .= '## Message'."\n\n";

        if ($mail['plain'] !== '') {
            $markdown .= "```\n";
            $markdown .= $this->escapeMarkdown($mail['plain'])."\n\n";
            $markdown .= "```\n";
        }

        return $markdown;
    }

    private function formatDate(string $date): string
    {
        if ($date === '') {
            return '';
        }

        try {
            /** @var string $timezone */
            $timezone = config()->string('app.timezone', 'UTC');
            $carbon = \Carbon\Carbon::parse($date)->timezone($timezone);

            return $carbon->toIso8601String();
        } catch (Throwable) {
            return $date;
        }
    }

    private function escapeMarkdown(string $text): string
    {
        return (string) preg_replace("/\n{3,}/", "\n\n", $text);
    }
}
