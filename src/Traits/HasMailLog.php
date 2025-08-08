<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use AchyutN\FilamentLogViewer\Enums\LogLevel;

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

    public static function parseMail(array $extractedLine, string $file): array
    {
        if (
            (array_key_exists('message', $extractedLine) && ! self::isMailStack($extractedLine['message'])) &&
            ! array_key_exists('date', $extractedLine) &&
            ! array_key_exists('env', $extractedLine)
        ) {
            return [];
        }

        $plainMail = '';
        $htmlMail = '';

        return [
            'date' => trim($extractedLine['date']),
            'env' => trim($extractedLine['env']),
            'log_level' => LogLevel::MAIL,
            'message' => self::extractMessage($extractedLine['message']),
            'stack' => '[]',
            'plain_mail' => $plainMail,
            'html_mail' => $htmlMail,
            'file' => $file,
        ];
    }
}
