<?php

declare(strict_types=1);

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\FileLogParser;

beforeEach(function () {
    $this->initializeLogs();
});

afterEach(function () {
    $this->deleteAllLogs();
});

describe('DefaultMailParser - isMailStack', function () {
    it('returns true for a full mail stack', function () {
        $parser = new DefaultMailParser;

        $raw = "From: hello@example.com\nTo: someone@example.com\nSubject: Hi\nMIME-Version: 1.0\nMessage-ID: <abc@x.com>";

        expect($parser->isMailStack($raw))->toBeTrue();
    });

    it('returns false when a keyword is missing', function () {
        $parser = new DefaultMailParser;

        expect($parser->isMailStack("From: hello@example.com\nSubject: Hi"))->toBeFalse();
    });

    it('returns false for null', function () {
        $parser = new DefaultMailParser;

        expect($parser->isMailStack(null))->toBeFalse();
    });
});

describe('DefaultMailParser - parseRaw', function () {
    it('extracts mail details from raw mail', function () {
        $parser = new DefaultMailParser;

        $raw = implode("\n", [
            'From: Achyut <achyut@example.com>',
            'To: Someone <someone@example.com>',
            'Subject: Test Subject',
            'MIME-Version: 1.0',
            'Date: Sat, 09 Aug 2025 00:18:13 +0545',
            'Message-ID: <abc@x.com>',
            'Content-Type: multipart/alternative; boundary=OcFZ3Fi3',
            '',
            '--OcFZ3Fi3',
            'Content-Type: text/plain; charset=UTF-8',
            '',
            'Body text',
            '',
            '--OcFZ3Fi3--',
        ]);

        $details = $parser->parseRaw($raw);

        expect($details)->not->toBeNull();
        expect($details['sender']['email'])->toBe('achyut@example.com');
        expect($details['receiver']['email'])->toBe('someone@example.com');
        expect($details['subject'])->toBe('Test Subject');
        expect($details['plain'])->toContain('Body text');
        expect($details['sent_date'])->toBeString();
    });
});

describe('DefaultMailParser - parse', function () {
    it('builds a mail log row', function () {
        $parser = new DefaultMailParser;

        $mailLine = [
            'date' => '2025-08-09 00:18:13',
            'env' => 'local',
            'message' => implode("\n", [
                'From: hello@example.com',
                'To: someone@example.com',
                'Subject: Hello',
                'MIME-Version: 1.0',
                'Message-ID: <abc@x.com>',
                'Content-Type: multipart/alternative; boundary=OcFZ3Fi3',
                '',
                '--OcFZ3Fi3',
                'Content-Type: text/plain; charset=UTF-8',
                '',
                'This is the body',
                '',
                '--OcFZ3Fi3--',
            ]),
        ];

        $row = $parser->parse($mailLine, 'laravel.log');

        expect($row)->not->toBeNull();
        expect($row['log_level'])->toBe(LogLevel::MAIL);
        expect($row['message'])->toBe('Hello');
        expect($row['file'])->toBe('laravel.log');
        expect($row['mail']['plain'])->toContain('This is the body');
    });
});

describe('DefaultMailParser - integration with stack trace parser', function () {
    it('parses a mail log written through the test helper', function () {
        $this->writeMailLog('mail.log');

        $parser = new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser);
        $rows = iterator_to_array($parser->parse(storage_path('logs/mail.log'), 'mail.log'));

        expect($rows)->toHaveCount(1);
        expect($rows[0]['log_level'])->toBe(LogLevel::MAIL);
        expect($rows[0]['mail'])->not->toBeNull();
    });
});
