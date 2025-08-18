<?php

declare(strict_types=1);

use AchyutN\FilamentLogViewer\Model\Log;

beforeEach(function () {
    $this->initializeLogs();
});

afterEach(function () {
    $this->deleteAllLogs();
});

describe('log files', function () {
    it('has logs in files with .log extension', function () {
        $laravelLog = file_get_contents(storage_path('logs/laravel.log'));
        $stackTraceLog = file_get_contents(storage_path('logs/stack-trace.log'));

        expect($laravelLog)
            ->toContain('local.ERROR: Sample log');

        expect($stackTraceLog)
            ->toContain('local.ERROR: Sample log with stack trace');
    });

    it('fetches log files from nested folders', function () {
        $this->writeLog('nested-folder/nested.log', '[2024-08-06 20:19:00] nested.NOTICE: Another notice log');

        $otherLog = file_get_contents(storage_path('logs/nested-folder/nested.log'));

        expect($otherLog)
            ->toContain('nested.NOTICE: Another notice log');
    });
});

describe('destroyAllLogs', function () {
    it('clears contents of all .log files', function () {
        Log::destroyAllLogs();

        $laravelLog = file_get_contents(storage_path('logs/laravel.log'));
        $otherLog = file_get_contents(storage_path('logs/other.log'));
        $notALog = file_get_contents(storage_path('logs/not-a-log.txt'));
        $stackTraceLog = file_get_contents(storage_path('logs/stack-trace.log'));

        expect($laravelLog)->toBe('');
        expect($otherLog)->toBe('');
        expect($notALog)->toBe('This is not a log');
        expect($stackTraceLog)->toBe('');
    });

    it('deletes nested log files', function () {
        $this->writeLog('nested-folder/nested.log', '[2024-08-06 20:19:00] nested.NOTICE: Another notice log');

        Log::destroyAllLogs();

        $nestedLog = file_get_contents(storage_path('logs/nested-folder/nested.log'));

        expect($nestedLog)->toBe('');
    });

    it('does nothing if log folder does not exist', function () {
        $this->deleteAllLogs();

        $directory = storage_path('logs');

        system('rm -rf '.escapeshellarg($directory));

        expect(fn () => Log::destroyAllLogs())->not->toThrow(Exception::class);
    });
});

describe('getRows', function () {
    it('does nothing if log folder does not exist', function () {
        $this->deleteAllLogs();

        $directory = storage_path('logs');

        system('rm -rf '.escapeshellarg($directory));

        expect(fn () => Log::getRows())->not->toThrow(Exception::class);
    });

    it('returns all logs from .log files', function () {
        $this->writeLog('nested-folder/nested.log', '[2024-08-06 20:19:00] nested.NOTICE: Another notice log');

        $logs = Log::getRows();

        expect($logs)->toBeArray();
        expect($logs)->toHaveCount(5);
        expect($logs)
            ->each
            ->toHaveKey('date')
            ->toHaveKey('env')
            ->toHaveKey('log_level')
            ->toHaveKey('message')
            ->toHaveKey('stack')
            ->toHaveKey('file');
        expect($logs)
            ->sequence(
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:19:00')
                        ->env->toBe('nested')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::NOTICE)
                        ->message->toBe('Another notice log')
                        ->stack->toBe('[]')
                        ->file->toBe('nested-folder/nested.log');
                },
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:18:00')
                        ->env->toBe('local')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::ERROR)
                        ->message->toBe('Another log with stack trace at /path/to/another_file.php:789')
                        ->stack->not->toBeNull()
                        ->file->toBe('stack-trace.log');
                },
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:17:00')
                        ->env->toBe('local')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::ERROR)
                        ->message->toBe('Sample log with stack trace at /path/to/file.php:123')
                        ->stack->not->toBeNull()
                        ->file->toBe('stack-trace.log');
                },
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:16:00')
                        ->env->toBe('local')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::INFO)
                        ->message->toBe('Another log')
                        ->stack->toBe('[]')
                        ->file->toBe('other.log');
                },
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:15:00')
                        ->env->toBe('local')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::ERROR)
                        ->message->toBe('Sample log')
                        ->stack->toBe('[]')
                        ->file->toBe('laravel.log');
                },
            );
    });

    it('returns an empty array if no log files exist', function () {
        $this->deleteAllLogs();

        $logs = Log::getRows();

        expect($logs)->toBeArray();
        expect($logs)->toBeEmpty();
    });

    it('skips non-log files', function () {
        $logs = Log::getRows();

        expect($logs)->toHaveCount(4);
        expect($logs)->not->toContain(fn ($log) => $log['file'] === 'not-a-log.txt');
    });
});

describe('getLogsByLogLevel', function () {
    it('returns all logs when log level is "all-logs"', function () {
        $logs = Log::getLogsByLogLevel();

        expect($logs)->toBeArray();
        expect($logs)->toHaveCount(4);
    });

    it('returns logs filtered by log level', function () {
        $errorLogs = Log::getLogsByLogLevel('info');

        expect($errorLogs)->toBeArray();
        expect($errorLogs)->toHaveCount(1);
        expect($errorLogs)
            ->each
            ->toHaveKey('date')
            ->toHaveKey('env')
            ->toHaveKey('log_level')
            ->toHaveKey('message')
            ->toHaveKey('stack')
            ->toHaveKey('file');
        expect($errorLogs)
            ->sequence(
                function ($log) {
                    return $log
                        ->date->toBe('2024-08-06 20:16:00')
                        ->env->toBe('local')
                        ->log_level->tobe(AchyutN\FilamentLogViewer\Enums\LogLevel::INFO)
                        ->message->toBe('Another log')
                        ->stack->toBe('[]')
                        ->file->toBe('other.log');
                }
            );
    });

    it('returns an empty array if no logs match the log level', function () {
        $logs = Log::getLogsByLogLevel('debug');

        expect($logs)->toBeArray();
        expect($logs)->toBeEmpty();
    });
});

describe('getLogCount', function () {
    it('returns the total count of logs', function () {
        $count = Log::getLogCount();

        expect($count)->toBe(4);
    });

    it('returns zero if no log files exist', function () {
        $this->deleteAllLogs();

        $count = Log::getLogCount();

        expect($count)->toBe(0);
    });

    it('returns zero if no logs match the log level', function () {
        $count = Log::getLogCount('debug');

        expect($count)->toBe(0);
    });
});
