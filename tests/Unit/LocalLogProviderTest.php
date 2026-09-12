<?php

declare(strict_types=1);

use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\FileLogParser;
use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->initializeLogs();
});

afterEach(function () {
    $this->deleteAllLogs();
});

function makeProvider(): LocalLogProvider
{
    return new LocalLogProvider(
        new FileLogParser(new DefaultMailParser, new DefaultStackTraceParser),
        new DefaultStackTraceParser,
    );
}

describe('LocalLogProvider - getRows', function () {
    it('returns all logs from .log files', function () {
        $logs = makeProvider()->getRows(true);

        expect($logs)->toBeArray();
        expect($logs)->toHaveCount(4);
        expect($logs)
            ->each
            ->toHaveKey('date')
            ->toHaveKey('env')
            ->toHaveKey('log_level')
            ->toHaveKey('message')
            ->toHaveKey('raw_stack')
            ->toHaveKey('has_stack')
            ->toHaveKey('file');
    });

    it('caches rows on subsequent calls', function () {
        $provider = makeProvider();

        $first = $provider->getRows(true);
        $second = $provider->getRows();

        expect($second)->toBe($first);
    });

    it('returns an empty array if no log files exist', function () {
        $this->deleteAllLogs();

        $logs = makeProvider()->getRows(true);

        expect($logs)->toBeArray();
        expect($logs)->toBeEmpty();
    });

    it('skips non-log files', function () {
        $logs = makeProvider()->getRows(true);

        expect($logs)->toHaveCount(4);
        expect($logs)->not->toContain(fn ($log) => $log['file'] === 'not-a-log.txt');
    });

    it('returns logs sorted by date descending', function () {
        $logs = makeProvider()->getRows(true);

        expect($logs[0]['date'])->toBe('2024-08-06 20:18:00');
    });
});

describe('LocalLogProvider - getLogsByLevel', function () {
    it('returns all logs when level is all-logs', function () {
        $logs = makeProvider()->getLogsByLevel('all-logs');

        expect($logs)->toBeArray();
        expect($logs)->toHaveCount(4);
    });

    it('returns logs filtered by level', function () {
        $logs = makeProvider()->getLogsByLevel('info');

        expect($logs)->toHaveCount(1);
        expect($logs[0]['log_level']->value)->toBe('info');
    });

    it('returns empty array when no match', function () {
        expect(makeProvider()->getLogsByLevel('debug'))->toBeEmpty();
    });
});

describe('LocalLogProvider - getCount', function () {
    it('returns total count', function () {
        expect(makeProvider()->getCount())->toBe(4);
    });

    it('returns null when zero', function () {
        $this->deleteAllLogs();

        expect(makeProvider()->getCount())->toBeNull();
    });

    it('returns null when level has no match', function () {
        expect(makeProvider()->getCount('debug'))->toBeNull();
    });
});

describe('LocalLogProvider - files', function () {
    it('getFiles returns all log files', function () {
        $files = makeProvider()->getFiles();

        expect($files)->toContain('laravel.log');
        expect($files)->toContain('stack-trace.log');
    });

    it('getFilesForFilter builds directory map', function () {
        $files = makeProvider()->getFilesForFilter();

        expect($files)->toHaveKey('laravel.log');
        expect($files)->toHaveKey('stack-trace.log');
    });

    it('excludes empty log files from getFiles and getFilesForFilter', function () {
        $this->writeLog('empty.log', '');

        $files = makeProvider()->getFiles();
        $filterFiles = makeProvider()->getFilesForFilter();

        expect($files)->not->toContain('empty.log');
        expect($filterFiles)->not->toHaveKey('empty.log');
        expect($files)->toContain('laravel.log');
    });
});

describe('LocalLogProvider - deleteAll', function () {
    it('clears all log files', function () {
        makeProvider()->deleteAll();

        expect(file_get_contents(storage_path('logs/laravel.log')))->toBe('');
        expect(file_get_contents(storage_path('logs/other.log')))->toBe('');
    });
});

describe('LocalLogProvider - truncate vs delete', function () {
    it('truncates files by default', function () {
        Config::set('filament-log-viewer.truncate_on_clear', true);

        makeProvider()->deleteAll();

        expect(file_exists(storage_path('logs/laravel.log')))->toBeTrue();
        expect(file_get_contents(storage_path('logs/laravel.log')))->toBe('');
    });

    it('deletes all log files when truncate_on_clear is false', function () {
        Config::set('filament-log-viewer.truncate_on_clear', false);

        makeProvider()->deleteAll();

        expect(file_exists(storage_path('logs/laravel.log')))->toBeFalse();
        expect(file_exists(storage_path('logs/other.log')))->toBeFalse();
        expect(file_exists(storage_path('logs/stack-trace.log')))->toBeFalse();
    });

    it('deletes only the given file when truncate_on_clear is false', function () {
        Config::set('filament-log-viewer.truncate_on_clear', false);

        makeProvider()->deleteFile('other.log');

        expect(file_exists(storage_path('logs/other.log')))->toBeFalse();
        expect(file_exists(storage_path('logs/laravel.log')))->toBeTrue();
        expect(file_exists(storage_path('logs/stack-trace.log')))->toBeTrue();
    });

    it('never deletes non-log files in delete mode', function () {
        Config::set('filament-log-viewer.truncate_on_clear', false);

        makeProvider()->deleteFile('not-a-log.txt');

        expect(file_exists(storage_path('logs/not-a-log.txt')))->toBeTrue();
    });
});

describe('LocalLogProvider - deleteFile', function () {
    it('clears only the given log file', function () {
        makeProvider()->deleteFile('laravel.log');

        expect(file_get_contents(storage_path('logs/laravel.log')))->toBe('');
        expect(file_get_contents(storage_path('logs/other.log')))->not->toBe('');
        expect(file_get_contents(storage_path('logs/stack-trace.log')))->not->toBe('');
    });

    it('clears nested log files', function () {
        $this->writeLog('nested-folder/nested.log', '[2024-08-06 20:19:00] nested.NOTICE: Another notice log');

        makeProvider()->deleteFile('nested-folder/nested.log');

        expect(file_get_contents(storage_path('logs/nested-folder/nested.log')))->toBe('');
    });

    it('ignores unknown, non-log, or unsafe paths', function () {
        $provider = makeProvider();

        $provider->deleteFile('missing.log');
        $provider->deleteFile('not-a-log.txt');
        $provider->deleteFile('../../.env');

        expect(file_get_contents(storage_path('logs/laravel.log')))->not->toBe('');
    });

    it('invalidates cached rows after clearing a file', function () {
        expect(makeProvider()->getRows(true))->toHaveCount(4);

        makeProvider()->deleteFile('other.log');

        $rows = makeProvider()->getRows(true);

        expect(collect($rows)->pluck('file'))->not->toContain('other.log');
    });
});

describe('LocalLogProvider - getStackFromRaw', function () {
    it('extracts stack traces from raw', function () {
        $raw = "[stacktrace]\n#0 /path/to/file.php(123): something()\n#1 /path/other.php(45): other()\n#2 {main}";

        $stack = makeProvider()->getStackFromRaw($raw);

        expect($stack)->toBeArray();
        expect($stack)->not->toBeEmpty();
    });
});

describe('LocalLogProvider - cache', function () {
    it('stores parsed rows in a single fingerprint slot', function () {
        makeProvider()->getRows(true);

        $stored = Cache::get('filament-log-viewer::rows');

        expect($stored)->toBeArray();
        expect($stored)->toHaveKeys(['fingerprint', 'rows']);
        expect($stored['rows'])->toHaveCount(4);
    });

    it('invalidates the cache when a log file changes', function () {
        expect(makeProvider()->getRows(true))->toHaveCount(4);

        $this->writeLog('laravel.log', '[2024-08-07 09:00:00] local.WARNING: New entry after cache');

        $rows = makeProvider()->getRows(true);

        expect($rows)->toHaveCount(4);
        expect(collect($rows)->pluck('message'))->toContain('New entry after cache');
        expect(collect($rows)->pluck('message'))->not->toContain('Sample log');
    });

    it('skips the cache store when disabled via config', function () {
        Config::set('filament-log-viewer.disable_cache', true);

        makeProvider()->getRows(true);

        expect(Cache::get('filament-log-viewer::rows'))->toBeNull();
    });
});
