---
name: filament-log-viewer-custom-viewer
description: Guide to building your own custom log viewer inside your Laravel app on top of achyutn/filament-log-viewer — custom DTO, parsers, table, and viewers.
license: MIT
tags:
  - filament
  - laravel
  - logs
  - extensibility
metadata:
  author: Achyut Neupane
---

# Building a Custom Log Viewer in Your Application

## Context

You want a custom log viewer inside your Laravel app on top of `achyutn/filament-log-viewer` — your own data model/DTO, parser, table columns, stack-trace and mail viewers, and actions. Everything lives in your app's namespace (`App\...`) and is wired through the plugin's override methods. Two routes exist:

- **Route A** — keep the `LogRow` array shape and reuse the default `LogTable`, swapping in your own provider, parser, schemas, and actions.
- **Route B** — use a completely custom DTO/object shape and your own table design by supplying your own page.

Both routes reuse the same contracts and plugin override methods.

## The seams (recap)

| Contract | Default | Plugin override |
|---|---|---|
| `LogProvider` | `LocalLogProvider` | `providerClass()` |
| `LogParser` | `FileLogParser` | `parserClass()` |
| `MailParser` | `DefaultMailParser` | `mailParserClass()` |
| `StackTraceParser` | `DefaultStackTraceParser` | `stackTraceParserClass()` |
| `LogViewerPage` | `LogTable` | `pageClass()` |
| `CanDeleteLogs` | (implemented by `LocalLogProvider`) | — |
| `LogTableSchemaInterface` | `LogTableSchema` | `tableSchemaClass()` |
| `LogEntrySchemaInterface` | `ErrorLogSchema`, `JSONLogSchema`, `MailLogSchema` | `errorSchemaClass()`, `jsonSchemaClass()`, `mailSchemaClass()` |
| `Filament\Actions\Action` | `CopyMarkdownAction` | `copyMarkdownActionClass()` |
| — | `DateRangeFilter`, `FileFilter` | `dateRangeFilterClass()`, `fileFilterClass()` |

Overrides are resolved through Laravel's service container (constructor dependencies auto-wire), and the plugin is a container singleton, so the classes apply app-wide rather than per-panel.

---

## Route A — keep the `LogRow` shape

Use this when your data fits the `LogRow` array shape (`date`, `env`, `log_level`, `message`, `description`, `context`, `raw_stack`, `has_stack`, `mail`, `file`). You keep the default table and swap everything else.

### 1. Implement your provider

```php
<?php

declare(strict_types=1);

namespace App\LogProviders;

use AchyutN\FilamentLogViewer\Contracts\CanDeleteLogs;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use App\Models\LogEntry;

final class MyLogProvider implements LogProvider, CanDeleteLogs
{
    public function getRows(bool $refresh = false): array
    {
        // Map your store's entries into the LogRow array shape.
        return LogEntry::query()->get()->map(
            fn (LogEntry $entry): array => [
                'date' => $entry->date,
                'env' => $entry->env,
                'log_level' => LogLevel::from($entry->level),
                'message' => $entry->message,
                'description' => $entry->description,
                'context' => $entry->context,
                'raw_stack' => $entry->raw_stack,
                'has_stack' => $entry->has_stack,
                'mail' => null,
                'file' => $entry->channel,
            ],
        )->all();
    }

    public function getLogsByLevel(string $level): array
    {
        return array_values(array_filter(
            $this->getRows(),
            fn (array $row): bool => $row['log_level']->value === $level,
        ));
    }

    public function getCount(string $level = 'all-logs'): ?int
    {
        return count($this->getRows()) ?: null;
    }

    public function getFiles(): array
    {
        return LogEntry::query()->distinct()->pluck('channel')->all();
    }

    public function getFilesForFilter(): array
    {
        return LogEntry::query()->distinct()->pluck('channel')->mapWithKeys(
            fn (string $channel): array => [$channel => $channel],
        )->all();
    }

    public function getStackFromRaw(string $rawStack): array
    {
        return [];
    }

    public function deleteAll(): void
    {
        LogEntry::query()->delete();
    }

    public function deleteFile(string $file): void
    {
        LogEntry::query()->where('channel', $file)->delete();
    }
}
```

### 2. Swap in your own schemas, actions, and filters

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\LogParsers\MyLogParser;
use App\LogParsers\MyMailParser;
use App\LogParsers\MyStackParser;
use App\LogProviders\MyLogProvider;
use App\LogSchemas\MyErrorSchema;
use App\LogSchemas\MyJsonSchema;
use App\LogSchemas\MyMailSchema;
use App\LogSchemas\MyTableSchema;

FilamentLogViewer::make()
    ->providerClass(MyLogProvider::class)
    ->parserClass(MyLogParser::class)             // extends FileLogParser
    ->mailParserClass(MyMailParser::class)        // extends DefaultMailParser
    ->stackTraceParserClass(MyStackParser::class) // extends DefaultStackTraceParser
    ->tableSchemaClass(MyTableSchema::class)       // implements LogTableSchemaInterface
    ->errorSchemaClass(MyErrorSchema::class)       // implements LogEntrySchemaInterface
    ->jsonSchemaClass(MyJsonSchema::class)
    ->mailSchemaClass(MyMailSchema::class);
```

The table still renders the `LogRow` keys, but the columns, modals, and actions are yours. Parsers and schemas can be swapped individually — extend a default and override a single `protected` method, or implement the contract from scratch.

---

## Route B — custom DTO and your own table

Use this when your data is a real object/DTO and you want your own columns. You supply your own page via `pageClass()` — the declared type is `class-string<LogTable>`, so the idiomatic path is to **extend `LogTable`**.

### 1. Define your DTO

```php
<?php

declare(strict_types=1);

namespace App\Data;

final class LogEntry
{
    public function __construct(
        public readonly string $occurredAt,
        public readonly string $channel,
        public readonly string $level,
        public readonly string $summary,
        public readonly ?array $meta = null,
    ) {}
}
```

### 2. Implement your provider (returns your DTOs)

```php
<?php

declare(strict_types=1);

namespace App\LogProviders;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use App\Data\LogEntry;

final class MyProvider implements LogProvider
{
    public function getRows(bool $refresh = false): array
    {
        return LogEntry::query()->get()->all(); // array<LogEntry>
    }

    public function getLogsByLevel(string $level): array
    {
        return array_values(array_filter(
            $this->getRows(),
            fn (LogEntry $entry): bool => $entry->level === $level,
        ));
    }

    public function getCount(string $level = 'all-logs'): ?int
    {
        return count($this->getRows()) ?: null;
    }

    public function getFiles(): array
    {
        return LogEntry::query()->distinct()->pluck('channel')->all();
    }

    public function getFilesForFilter(): array
    {
        return LogEntry::query()->distinct()->pluck('channel')->mapWithKeys(
            fn (string $channel): array => [$channel => $channel],
        )->all();
    }

    public function getStackFromRaw(string $rawStack): array
    {
        return [];
    }
}
```

Implement `CanDeleteLogs` only if your store supports deletion (see the provider examples). Your page — not the plugin — decides how to render these objects.

### 3. Build your page (extend `LogTable`)

```php
<?php

declare(strict_types=1);

namespace App\Pages;

use AchyutN\FilamentLogViewer\LogTable;
use App\LogProviders\MyProvider;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class MyLogTable extends LogTable
{
    public function table(Table $table): Table
    {
        return $table
            ->records(
                function (?array $filters, ?string $sortColumn, ?string $sortDirection, ?string $search, int $page, int $recordsPerPage): LengthAwarePaginator {
                    /** @var array<int, \App\Data\LogEntry> $rows */
                    $rows = app(MyProvider::class)->getRows();

                    // Filter, sort, and paginate your DTOs here.
                    $paginated = collect($rows)
                        ->sortByDesc('occurredAt')
                        ->forPage($page, $recordsPerPage);

                    return new LengthAwarePaginator(
                        $paginated,
                        total: count($rows),
                        perPage: $recordsPerPage,
                        currentPage: $page,
                    );
                })
            ->columns([
                TextColumn::make('occurredAt')->label('Occurred')->since(),
                TextColumn::make('channel')->label('Channel')->badge(),
                TextColumn::make('level')->label('Level')->badge(),
                TextColumn::make('summary')->label('Summary')->wrap(),
            ])
            ->recordActions([
                // Your own row actions, e.g. an "inspect" slide-over.
            ]);
    }
}
```

Because `MyLogTable extends LogTable`, it inherits the navigation, tabs, and header actions. Override any of the `protected` factory methods (`getViewAction()`, `getViewJsonAction()`, `getReadMailAction()`, `getDateRangeFilter()`, `getFileFilter()`, `getHeaderActions()`) to tailor them. You can also build a fresh Filament page implementing `HasTable`/`LogViewerPage` instead — the contracts do not force `LogTable`.

### 4. Register everything in your panel provider

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Pages\MyLogTable;
use App\LogProviders\MyProvider;

return $panel->plugins([
    FilamentLogViewer::make()
        ->pageClass(MyLogTable::class)
        ->providerClass(MyProvider::class)
        ->navigationLabel('My Logs'),
]);
```

## Notes

- Overrides resolve through Laravel's service container, so constructor dependencies auto-wire.
- The plugin is a container singleton, so the classes apply app-wide rather than per-panel.
- Route B pages extend `LogTable` and keep the navigation/tabs/header-action machinery; the `LogRow` array shape is then irrelevant to your page because you define the columns.
- `AchyutN\FilamentLogViewer\Model\Log` stays as a backward-compatible static facade; prefer the `LogProvider` contract in new code.
- Write tests for your custom classes like any other Laravel code; the plugin's own suite covers the default wiring.

## References

- Skill: `resources/boost/skills/filament-log-viewer-development/SKILL.md`
- Code examples: `references/code-examples.md`
- Provider examples: `references/providers/read-only-provider.md`, `references/providers/database-provider.md`
- Contracts: `src/Contracts/*`
- Page: `src/LogTable.php`
