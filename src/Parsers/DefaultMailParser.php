<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Parsers;

use AchyutN\FilamentLogViewer\Contracts\MailParser;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Carbon\Carbon;

/**
 * @phpstan-type LogRow array{
 *     date: string,
 *     env: string,
 *     log_level: LogLevel,
 *     message: string,
 *     description: string|null,
 *     mail: array{plain: string, html: string, sender: array{name: string, email: string}|null, receiver: array{name: string, email: string}|null, subject: string, sent_date: string}|null,
 *     context: array<string, mixed>|null,
 *     raw_stack: string,
 *     has_stack: bool,
 *     file: string
 * }
 * @phpstan-type MailDetails array{
 *     plain: string,
 *     html: string,
 *     sender: array{name: string, email: string}|null,
 *     receiver: array{name: string, email: string}|null,
 *     subject: string,
 *     sent_date: string
 * }
 */
class DefaultMailParser implements MailParser
{
    /**
     * @param  array{date: string, env: string, message: string}  $mailLine
     * @return LogRow|null
     */
    public function parse(array $mailLine, string $file): ?array
    {
        if (
            (array_key_exists('message', $mailLine) && ! $this->isMailStack($mailLine['message'])) &&
            ! array_key_exists('date', $mailLine) &&
            ! array_key_exists('env', $mailLine)
        ) {
            return null;
        }

        $raw = $mailLine['message'];
        $date = $mailLine['date'] ?? '';
        $env = $mailLine['env'] ?? '';

        return $this->parseMailLines($raw, $date, $env, $file);
    }

    public function isMailStack(?string $logStack): bool
    {
        if (! isset($logStack)) {
            return false;
        }

        $keywords = [
            'From:',
            'To:',
            'Subject:',
            'MIME-Version:',
            'Message-ID:',
        ];

        return array_all($keywords, fn ($keyword): bool => str_contains($logStack, $keyword));
    }

    /**
     * @return MailDetails|null
     */
    public function parseRaw(string $raw): ?array
    {
        $sender = '';
        $receiver = '';
        $subject = '';
        $mailDate = '';

        if (preg_match('/^From:\s*(.+)$/mi', $raw, $m)) {
            $sender = mb_trim($m[1]);
        }
        if (preg_match('/^To:\s*(.+)$/mi', $raw, $m)) {
            $receiver = mb_trim($m[1]);
        }
        if (preg_match('/^Subject:\s*(.+)$/mi', $raw, $m)) {
            $subject = mb_trim($m[1]);
        }
        if (preg_match('/^Date:\s*(.+)$/mi', $raw, $m)) {
            $mailDate = mb_trim($m[1]);
            $carbon = Carbon::parse($mailDate);
            $timezone = is_string(config('app.timezone')) ? config('app.timezone') : 'UTC';
            $carbon->setTimezone($timezone);
            $mailDate = $carbon->format('Y-m-d h:i:s A');
        }

        [$plainMail, $htmlMail] = $this->extractMail($raw);

        $markdownPlain = preg_replace('/\r\n|\r|\n/', "\n", $plainMail);

        return [
            'plain' => $markdownPlain ?? '',
            'html' => $htmlMail,
            'sender' => $this->extractNameAndEmail($sender),
            'receiver' => $this->extractNameAndEmail($receiver),
            'subject' => $subject,
            'sent_date' => $mailDate,
        ];
    }

    /**
     * @return LogRow
     */
    protected function parseMailLines(string $raw, string $date, string $env, string $file): array
    {
        $sender = '';
        $receiver = '';
        $subject = '';
        $mailDate = '';

        if (preg_match('/^From:\s*(.+)$/mi', $raw, $m)) {
            $sender = mb_trim($m[1]);
        }
        if (preg_match('/^To:\s*(.+)$/mi', $raw, $m)) {
            $receiver = mb_trim($m[1]);
        }
        if (preg_match('/^Subject:\s*(.+)$/mi', $raw, $m)) {
            $subject = mb_trim($m[1]);
        }
        if (preg_match('/^Date:\s*(.+)$/mi', $raw, $m)) {
            $mailDate = mb_trim($m[1]);
            $carbon = Carbon::parse($mailDate);
            $timezone = is_string(config('app.timezone')) ? config('app.timezone') : 'UTC';
            $carbon->setTimezone($timezone);
            $mailDate = $carbon->format('Y-m-d h:i:s A');
        }

        [$plainMail, $htmlMail] = $this->extractMail($raw);

        $markdownPlain = preg_replace('/\r\n|\r|\n/', "\n", $plainMail);

        return [
            'date' => mb_trim($date),
            'env' => mb_trim($env),
            'log_level' => LogLevel::MAIL,
            'message' => $subject,
            'description' => null,
            'mail' => [
                'plain' => $markdownPlain ?? '',
                'html' => $htmlMail,
                'sender' => $this->extractNameAndEmail($sender),
                'receiver' => $this->extractNameAndEmail($receiver),
                'subject' => $subject,
                'sent_date' => $mailDate,
            ],
            'has_stack' => false,
            'raw_stack' => '',
            'context' => null,
            'file' => $file,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function extractMail(string $raw): array
    {
        $plainMail = '';
        $htmlMail = '';

        $boundary = preg_match('/boundary=([^\s]+)/', $raw, $matches) ? mb_trim($matches[1], '"') : null;

        if ($boundary) {
            /** @var list<string> $parts */
            $parts = preg_split('/--'.preg_quote($boundary, '/').'/', $raw);

            foreach ($parts as $part) {
                $part = mb_trim($part);

                if (mb_stripos($part, 'Content-Type: text/plain') !== false) {
                    $plainMail = mb_trim((string) preg_replace('/^.*?\r?\n\r?\n/s', '', $part));
                    $plainMail = preg_replace('/^Content-(Type|Transfer-Encoding):.*\r?\n?/mi', '', $plainMail);
                }

                if (mb_stripos($part, 'Content-Type: text/html') !== false) {
                    $htmlMail = mb_trim((string) preg_replace('/^.*?\r?\n\r?\n/s', '', $part));
                    $htmlMail = preg_replace('/^Content-(Type|Transfer-Encoding):.*\r?\n?/mi', '', $htmlMail);
                }
            }
        }

        $plainMail = (string) $plainMail;
        $plainMail = preg_replace('/\r\n|\r|\n/', "\n\n", $plainMail);

        return [$plainMail ?? '', $htmlMail ?? ''];
    }

    /**
     * @return array{name: string, email: string}
     */
    protected function extractNameAndEmail(string $address): array
    {
        if (preg_match('/^(.*?)\s*<([^>]+)>$/', $address, $matches)) {
            $name = mb_trim($matches[1]);
            $email = mb_trim($matches[2]);
        } else {
            $name = '';
            $email = mb_trim($address);
        }

        return [
            'name' => $name,
            'email' => $email,
        ];
    }
}
