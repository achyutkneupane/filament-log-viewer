<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Carbon\Carbon;

/**
 * @phpstan-import-type LogRow from Log
 *
 * @phpstan-type MailLine array{
 *      date: string,
 *      env: string,
 *      message: string,
 *   }
 */
trait HasMailLog
{
    public static function isMailStack(?string $logStack): bool
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

        foreach ($keywords as $keyword) {
            if (! str_contains($logStack, $keyword)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  MailLine  $extractedLine
     * @return LogRow|null
     */
    public static function parseMail(array $extractedLine, string $file): ?array
    {
        if (
            (array_key_exists('message', $extractedLine) && ! self::isMailStack($extractedLine['message'])) &&
            ! array_key_exists('date', $extractedLine) &&
            ! array_key_exists('env', $extractedLine)
        ) {
            return null;
        }

        $raw = $extractedLine['message'];
        $date = $extractedLine['date'] ?? '';
        $env = $extractedLine['env'] ?? '';

        return self::parseMailLines($raw, $date, $env, $file);
    }

    /** @return LogRow */
    private static function parseMailLines(string $raw, string $date, string $env, string $file): array
    {
        [$plainMail, $htmlMail] = self::extractMail($raw);

        $sender = '';
        $receiver = '';
        $subject = '';
        $mailDate = '';

        if (preg_match('/^From:\s*(.+)$/mi', $raw, $m)) {
            $sender = trim($m[1]);
        }
        if (preg_match('/^To:\s*(.+)$/mi', $raw, $m)) {
            $receiver = trim($m[1]);
        }
        if (preg_match('/^Subject:\s*(.+)$/mi', $raw, $m)) {
            $subject = trim($m[1]);
        }
        if (preg_match('/^Date:\s*(.+)$/mi', $raw, $m)) {
            $mailDate = trim($m[1]);
            $carbon = Carbon::parse($mailDate);
            /** @var string $timezone */
            $timezone = config('app.timezone');
            $carbon->setTimezone($timezone);
            $mailDate = $carbon->format('Y-m-d h:i:s A');
        }

        $markdownPlain = preg_replace('/\r\n|\r|\n/', "\n", (string) $plainMail);

        return [
            'date' => trim($date),
            'env' => trim($env),
            'log_level' => LogLevel::MAIL,
            'message' => $subject,
            'mail' => [
                'plain' => $markdownPlain ?? '',
                'html' => $htmlMail,
                'sender' => self::extractNameAndEmail($sender),
                'receiver' => self::extractNameAndEmail($receiver),
                'subject' => $subject,
                'sent_date' => $mailDate,
            ],
            'stack' => [],
            'context' => null,
            'file' => $file,
        ];
    }

    /** @return array{0: string, 1: string} */
    private static function extractMail(string $raw): array
    {
        $plainMail = '';
        $htmlMail = '';

        $boundary = preg_match('/boundary=([^\s]+)/', $raw, $matches) ? trim($matches[1], '"') : null;

        if ($boundary) {
            /** @var list<string> $parts */
            $parts = preg_split('/--'.preg_quote($boundary, '/').'/', $raw);

            foreach ($parts as $part) {
                $part = trim($part);

                if (mb_stripos($part, 'Content-Type: text/plain') !== false) {
                    $plainMail = trim((string) preg_replace('/^.*?\r?\n\r?\n/s', '', $part));
                    $plainMail = preg_replace('/^Content-(Type|Transfer-Encoding):.*\r?\n?/mi', '', $plainMail);
                }

                if (mb_stripos($part, 'Content-Type: text/html') !== false) {
                    $htmlMail = trim((string) preg_replace('/^.*?\r?\n\r?\n/s', '', $part));
                    $htmlMail = preg_replace('/^Content-(Type|Transfer-Encoding):.*\r?\n?/mi', '', $htmlMail);
                }
            }
        }

        $plainMail = (string) $plainMail;
        $plainMail = preg_replace('/\r\n|\r|\n/', "\n\n", $plainMail);

        return [$plainMail ?? '', $htmlMail ?? ''];
    }

    /** @return array{name: string, email: string} */
    private static function extractNameAndEmail(string $address): array
    {
        if (preg_match('/^(.*?)\s*<([^>]+)>$/', $address, $matches)) {
            $name = trim($matches[1]);
            $email = trim($matches[2]);
        } else {
            $name = '';
            $email = trim($address);
        }

        return [
            'name' => $name,
            'email' => $email,
        ];
    }
}
