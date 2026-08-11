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

describe('FileLogParser - isMailEntry', function () {
    it('detects mail entries', function () {
        $parser = new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser);

        expect($parser->isMailEntry("From: hello@example.com\nTo: someone@example.com\nSubject: Hi\nMIME-Version: 1.0\nMessage-ID: <abc@x.com>"))->toBeTrue();
        expect($parser->isMailEntry('Sample log message'))->toBeFalse();
    });
});

describe('FileLogParser - parse', function () {
    it('yields log rows from a file', function () {
        $parser = new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser);

        $rows = iterator_to_array($parser->parse(storage_path('logs/laravel.log'), 'laravel.log'));

        expect($rows)->toHaveCount(1);
        expect($rows[0]['date'])->toBe('2024-08-06 20:15:00');
        expect($rows[0]['env'])->toBe('local');
        expect($rows[0]['log_level'])->toBe(LogLevel::ERROR);
        expect($rows[0]['message'])->toBe('Sample log');
        expect($rows[0]['file'])->toBe('laravel.log');
    });

    it('parses stack trace entries', function () {
        $parser = new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser);

        $rows = iterator_to_array($parser->parse(storage_path('logs/stack-trace.log'), 'stack-trace.log'));

        expect($rows)->toHaveCount(2);
        expect($rows[0]['has_stack'])->toBeTrue();
        expect($rows[0]['raw_stack'])->toContain('[stacktrace]');
    });

    it('returns empty array for nonexistent file', function () {
        $parser = new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser);

        $rows = iterator_to_array($parser->parse(storage_path('logs/missing.log'), 'missing.log'));

        expect($rows)->toBeArray();
        expect($rows)->toBeEmpty();
    });
});
