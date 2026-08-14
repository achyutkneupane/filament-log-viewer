<?php

declare(strict_types=1);

arch('no dd, dump, or ray calls')
    ->expect(['dd', 'dump', 'ray'])
    ->each
    ->not
    ->toBeUsed();

arch('enums are string backed')
    ->expect('AchyutN\FilamentLogViewer\Enums')
    ->toBeStringBackedEnums();

arch('traits are of type trait')
    ->expect('AchyutN\FilamentLogViewer\Traits')
    ->toBeTraits();

arch('all classes are final')
    ->expect('AchyutN\FilamentLogViewer')
    ->classes()
    ->toBeFinal()
    ->ignoring([
        AchyutN\FilamentLogViewer\Actions\ClearAllLogsAction::class,
        AchyutN\FilamentLogViewer\Actions\ClearFileAction::class,
        AchyutN\FilamentLogViewer\Actions\CopyMarkdownAction::class,
        AchyutN\FilamentLogViewer\Filters\DateRangeFilter::class,
        AchyutN\FilamentLogViewer\Filters\FileFilter::class,
        AchyutN\FilamentLogViewer\LogTable::class,
        AchyutN\FilamentLogViewer\Parsers\DefaultMailParser::class,
        AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser::class,
        AchyutN\FilamentLogViewer\Parsers\FileLogParser::class,
        AchyutN\FilamentLogViewer\Providers\LocalLogProvider::class,
        AchyutN\FilamentLogViewer\Schema\ErrorLogSchema::class,
        AchyutN\FilamentLogViewer\Schema\JSONLogSchema::class,
        AchyutN\FilamentLogViewer\Schema\LogTableSchema::class,
        AchyutN\FilamentLogViewer\Schema\MailLogSchema::class,
    ]);
