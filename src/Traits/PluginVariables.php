<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use AchyutN\FilamentLogViewer\Actions\CopyMarkdownAction;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Contracts\MailParser;
use AchyutN\FilamentLogViewer\Contracts\StackTraceParser;
use AchyutN\FilamentLogViewer\Filters\DateRangeFilter;
use AchyutN\FilamentLogViewer\Filters\FileFilter;
use AchyutN\FilamentLogViewer\LogTable;
use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\FileLogParser;
use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;
use AchyutN\FilamentLogViewer\Schema\ErrorLogSchema;
use AchyutN\FilamentLogViewer\Schema\JSONLogSchema;
use AchyutN\FilamentLogViewer\Schema\LogTableSchema;
use AchyutN\FilamentLogViewer\Schema\MailLogSchema;
use BackedEnum;
use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use UnitEnum;

trait PluginVariables
{
    use EvaluatesClosures;

    public bool|Closure $authorized = true;

    public string|UnitEnum|Closure|null $navigationGroup = null;

    public string|BackedEnum|Closure $navigationIcon = 'heroicon-o-document-text';

    public string|Closure|null $navigationLabel = null;

    public int|Closure $navigationSort = 9999;

    public string|Closure $navigationUrl = '/logs';

    public string|null|Closure $pollingTime = null;

    public bool|null|Closure $registerNavigation = true;

    public string|Closure|null $pageClass = LogTable::class;

    public string|Closure|null $providerClass = LocalLogProvider::class;

    public string|Closure|null $parserClass = FileLogParser::class;

    public string|Closure|null $mailParserClass = DefaultMailParser::class;

    public string|Closure|null $stackTraceParserClass = DefaultStackTraceParser::class;

    public string|Closure|null $tableSchemaClass = LogTableSchema::class;

    public string|Closure|null $errorSchemaClass = ErrorLogSchema::class;

    public string|Closure|null $jsonSchemaClass = JSONLogSchema::class;

    public string|Closure|null $mailSchemaClass = MailLogSchema::class;

    public string|Closure|null $copyMarkdownActionClass = CopyMarkdownAction::class;

    public string|Closure|null $dateRangeFilterClass = DateRangeFilter::class;

    public string|Closure|null $fileFilterClass = FileFilter::class;

    public function isAuthorized(): bool
    {
        return (bool) $this->evaluate($this->authorized);
    }

    public function getNavigationGroup(): string|UnitEnum|null
    {
        /** @var string|UnitEnum|null */
        return $this->evaluate($this->navigationGroup);
    }

    public function getNavigationIcon(): string|BackedEnum
    {
        /** @var string|BackedEnum */
        return $this->evaluate($this->navigationIcon);
    }

    public function getNavigationLabel(): string
    {
        /** @phpstan-var string */
        return $this->evaluate($this->navigationLabel) ?? '';
    }

    public function getNavigationSort(): int
    {
        /** @var int */
        return $this->evaluate($this->navigationSort);
    }

    public function getNavigationUrl(): string
    {
        /** @var string */
        return $this->evaluate($this->navigationUrl);
    }

    public function getPollingTime(): ?string
    {
        /** @var string|null */
        return $this->evaluate($this->pollingTime);
    }

    public function shouldRegisterNavigation(): bool
    {
        return (bool) $this->evaluate($this->registerNavigation);
    }

    /** @return class-string<LogTable> */
    public function getPageClass(): string
    {
        /** @var class-string<LogTable>|null $class */
        $class = $this->evaluate($this->pageClass);

        return $class ?? LogTable::class;
    }

    /** @return class-string<LogProvider> */
    public function getProviderClass(): string
    {
        /** @var class-string<LogProvider>|null $class */
        $class = $this->evaluate($this->providerClass);

        return $class ?? LocalLogProvider::class;
    }

    /** @return class-string<FileLogParser> */
    public function getParserClass(): string
    {
        /** @var class-string<FileLogParser>|null $class */
        $class = $this->evaluate($this->parserClass);

        return $class ?? FileLogParser::class;
    }

    /** @return class-string<MailParser> */
    public function getMailParserClass(): string
    {
        /** @var class-string<MailParser>|null $class */
        $class = $this->evaluate($this->mailParserClass);

        return $class ?? DefaultMailParser::class;
    }

    /** @return class-string<StackTraceParser> */
    public function getStackTraceParserClass(): string
    {
        /** @var class-string<StackTraceParser>|null $class */
        $class = $this->evaluate($this->stackTraceParserClass);

        return $class ?? DefaultStackTraceParser::class;
    }

    /** @return class-string<LogTableSchema> */
    public function getTableSchemaClass(): string
    {
        /** @var class-string<LogTableSchema>|null $class */
        $class = $this->evaluate($this->tableSchemaClass);

        return $class ?? LogTableSchema::class;
    }

    /** @return class-string<ErrorLogSchema> */
    public function getErrorSchemaClass(): string
    {
        /** @var class-string<ErrorLogSchema>|null $class */
        $class = $this->evaluate($this->errorSchemaClass);

        return $class ?? ErrorLogSchema::class;
    }

    /** @return class-string<JSONLogSchema> */
    public function getJsonSchemaClass(): string
    {
        /** @var class-string<JSONLogSchema>|null $class */
        $class = $this->evaluate($this->jsonSchemaClass);

        return $class ?? JSONLogSchema::class;
    }

    /** @return class-string<MailLogSchema> */
    public function getMailSchemaClass(): string
    {
        /** @var class-string<MailLogSchema>|null $class */
        $class = $this->evaluate($this->mailSchemaClass);

        return $class ?? MailLogSchema::class;
    }

    /** @return class-string<CopyMarkdownAction> */
    public function getCopyMarkdownActionClass(): string
    {
        /** @var class-string<CopyMarkdownAction>|null $class */
        $class = $this->evaluate($this->copyMarkdownActionClass);

        return $class ?? CopyMarkdownAction::class;
    }

    /** @return class-string<DateRangeFilter> */
    public function getDateRangeFilterClass(): string
    {
        /** @var class-string<DateRangeFilter>|null $class */
        $class = $this->evaluate($this->dateRangeFilterClass);

        return $class ?? DateRangeFilter::class;
    }

    /** @return class-string<FileFilter> */
    public function getFileFilterClass(): string
    {
        /** @var class-string<FileFilter>|null $class */
        $class = $this->evaluate($this->fileFilterClass);

        return $class ?? FileFilter::class;
    }
}
