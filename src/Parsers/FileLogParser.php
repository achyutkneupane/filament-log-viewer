<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Parsers;

use AchyutN\FilamentLogViewer\Contracts\LogParser;
use AchyutN\FilamentLogViewer\Contracts\MailParser;
use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use Generator;
use Illuminate\Support\Str;

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
 */
class FileLogParser implements LogParser
{
    public function __construct(
        private readonly MailParser $mailParser,
        private readonly StackTraceParser $stackTraceParser
    ) {}

    /**
     * @return Generator<int, LogRow>
     */
    public function parse(string $filePath, string $fileName): Generator
    {
        $handle = @fopen($filePath, 'r');
        if ($handle === false) {
            return;
        }

        $entryLines = [];

        while (($line = fgets($handle)) !== false) {
            $line = mb_rtrim($line, "\r\n");

            if (($line[0] ?? '') === '[' && ($line[20] ?? '') === ']' && $entryLines !== []) {
                $parsed = $this->parseLogEntry($entryLines, $fileName);
                if ($parsed) {
                    yield $parsed;
                }
                $entryLines = [];
            }
            $entryLines[] = $line;
        }

        if ($entryLines !== []) {
            $parsed = $this->parseLogEntry($entryLines, $fileName);
            if ($parsed) {
                yield $parsed;
            }
        }

        fclose($handle);
    }

    public function isMailEntry(string $message): bool
    {
        return $this->mailParser->isMailStack($message);
    }

    /**
     * @param  array<int, string>  $lines
     * @return LogRow|null
     */
    protected function parseLogEntry(array $lines, string $file): ?array
    {
        $entry = implode("\n", $lines);

        preg_match('/\[(?<date>[\d\-:\s]+)\]\s(?<env>\w+)\.(?<level>\w+):\s(?<message>.*)/s', $entry, $matches);

        // @phpstan-ignore-next-line
        if (! isset($matches['level']) || ! isset($matches['message']) || ! isset($matches['date']) || ! isset($matches['env'])) {
            return null;
        }

        if ($this->mailParser->isMailStack($matches['message'])) {
            $mailLine = [
                'date' => $matches['date'],
                'env' => $matches['env'],
                'message' => $matches['message'],
            ];

            return $this->mailParser->parse($mailLine, $file);
        }

        $messagePart = mb_trim($matches['message']);

        [$message, $description, $context] = $this->splitMessagesAndContext($messagePart);

        return [
            'date' => mb_trim($matches['date']),
            'env' => mb_trim($matches['env']),
            'log_level' => LogLevel::from(mb_strtolower(mb_trim($matches['level']))),
            'message' => $message,
            'description' => $description,
            'context' => $context,
            'mail' => null,
            'has_stack' => $this->stackTraceParser->hasStack($matches['message']),
            'raw_stack' => $matches['message'],
            'file' => $file,
        ];
    }

    /**
     * @return array{0: string, 1: string|null, 2: array<string, mixed>|null}
     */
    protected function splitMessagesAndContext(string $raw): array
    {
        $pattern = '/^(?<message>.*?)(?<json>\{.*\})$/s';

        if (preg_match($pattern, $raw, $matches)) {
            $message = mb_trim($matches['message']);
            $json = mb_trim($matches['json']);
            $decoded = json_decode($json, true);

            $jsonFirstLine = (string) strtok($json, "\n");

            $regex = '/"exception":"\[object\] \(.*?\(code: \d+\): (?<real_msg>.*?) (?<loc>at\s\/.*?)\)$/s';

            if (preg_match($regex, $jsonFirstLine, $stackMatches)) {
                $description = $this->shortenPath(mb_trim($stackMatches['loc']));
                $message = mb_trim($stackMatches['real_msg']);
            } else {
                $description = null;
            }

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                $context = null;
            } else {
                /** @var array<string, mixed> $context */
                $context = array_map(fn ($value): mixed => is_string($value) && $this->looksLikeJson($value)
                    ? json_decode($value, true) ?? $value
                    : $value,
                    $decoded
                );
            }

            return [$message, $description, $context];
        }

        return [mb_trim($raw), null, null];
    }

    protected function looksLikeJson(string $value): bool
    {
        $value = mb_trim($value);

        return
            (str_starts_with($value, '{') && str_ends_with($value, '}')) ||
            (str_starts_with($value, '[') && str_ends_with($value, ']'));
    }

    protected function shortenPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::of($path)->after(base_path().DIRECTORY_SEPARATOR)->toString();
    }
}
