---
name: filament-log-viewer-development
description: Build, customize, and extend the Filament Log Viewer plugin for Filament v5, including swapping providers, parsers, schemas, actions, and pages through its contract-driven API.
license: MIT
tags:
  - filament
  - laravel
  - logs
  - developer-tool
  - extensibility
metadata:
  author: Achyut Neupane
---

# Filament Log Viewer Development

## Context

You are working in a Laravel app using `achyutn/filament-log-viewer` — a Filament v5 plugin to view and manage Laravel log files with stack traces, mail previews, filtering, and a contract-driven API for swapping the page, provider, parsers, schemas, and actions.

## Rules

- Version compatibility: `^2.x` targets Filament v5 (PHP ≥8.2), `^1.x` targets v4, `^0.x` targets v3.
- Register the plugin on the panel with `->plugins([FilamentLogViewer::make()])` (`src/FilamentLogViewer.php`).
- Customize navigation via `->navigationGroup()`, `->navigationIcon()` (string or `Heroicon` enum), `->navigationLabel()`, `->navigationSort()`, `->navigationUrl()`, and `->registerNavigation(false)` to hide it from the sidebar (`src/Traits/PluginVariables.php`, `src/Traits/HasLogViewerNavigation.php`).
- Control access with `->authorize()`; never leave the viewer open to all users.
- The table lives at `/logs` and supports log-level tabs, date/file filters, and a per-file clear dropdown.
- Every component is replaceable through a plugin override method by implementing its contract or extending the non-final default (`src/Traits/PluginVariables.php`).
- A provider that can delete logs must implement `CanDeleteLogs`; read-only providers (Pail, cloud readers) omit it and the clear buttons are hidden automatically (`src/Contracts/CanDeleteLogs.php`).
- `AchyutN\FilamentLogViewer\Model\Log` remains as a backward-compatible static facade delegating to the container-resolved `LogProvider`; prefer the contract in new code (`src/Model/Log.php`).

## Extending

Contracts and their defaults:

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

Additional overrides: `copyMarkdownActionClass()`, `dateRangeFilterClass()`, `fileFilterClass()`.

Copy/paste examples for every contract: `references/code-examples.md`. Provider examples by type: `references/providers/read-only-provider.md` and `references/providers/database-provider.md`.

> **The row shape.** Every row a provider returns is the `LogRow` array shape (`date`, `env`, `log_level`, `message`, `description`, `context`, `raw_stack`, `has_stack`, `mail`, `file`), and the default table renders those keys. For a database-backed provider, `file` carries the source/channel identifier of each row. To use a completely custom DTO/object shape with your own table design, supply your own page via `pageClass()` — see `references/custom-viewer.md`.

## Examples

### Installation

```bash
composer require achyutn/filament-log-viewer
```

### Plugin Registration

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

return $panel
    ->plugins([
        FilamentLogViewer::make(),
    ]);
```

### Full Configuration

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;
use Filament\Support\Icons\Heroicon;

FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->user()->is_admin)
    ->registerNavigation(true)
    ->navigationGroup('System')
    ->navigationIcon(Heroicon::OutlinedDocument)
    ->navigationLabel('Log Viewer')
    ->navigationSort(10)
    ->navigationUrl('/logs')
    ->pollingTime('60s');
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=filament-log-viewer-config
```

Then edit `config/filament-log-viewer.php`:

```php
return [
    'max_log_file_size' => env('LOG_MAX_SIZE_KB', 2048),
    'enable_delete' => env('LOG_ENABLE_DELETE', true),
    'enable_copy_markdown' => env('LOG_ENABLE_COPY_MARKDOWN', true),
    'disable_cache' => env('LOG_DISABLE_CACHE', false),
    'copy_markdown_levels' => explode(',', env('LOG_COPY_MARKDOWN_LEVELS', 'error')),
];
```

Available log levels: `error`, `warning`, `critical`, `alert`, `emergency`, `info`, `notice`, `debug`, `mail`.

## Anti-patterns / Gotchas

- Using the wrong package version for your Filament version — always check the compatibility table.
- Not disabling `enable_delete` in production — risks accidental log deletion.
- Missing `authorize()` — exposes logs to all users including customers.
- Implementing `CanDeleteLogs` on a read-only provider — shows misleading clear buttons.
- Calling `Log::getRows()` and other `Log::` statics in new code — prefer the `LogProvider` contract or a plugin override.
- Overriding a default class without respecting its non-final, `protected`-hook convention.
- Setting very large `max_log_file_size` without considering memory constraints.

## References

- Official Documentation: https://filamentphp.com/plugins/achyutn-log-viewer
- GitHub Repository: https://github.com/achyutkneupane/filament-log-viewer
- Laravel Boost: https://laravel.com/docs/boost
- Skill examples: `references/`
