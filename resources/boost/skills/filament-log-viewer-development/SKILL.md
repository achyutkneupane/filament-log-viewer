---
name: filament-log-viewer-development
description: Build and work with Filament Log Viewer plugin for Filament v5.
tags:
  - filament
  - laravel
  - logs
  - developer-tool
---

# Filament Log Viewer Development

Use this skill when working with the Filament Log Viewer package - a Filament plugin to view and manage Laravel log files.

## Context

A developer-focused Laravel log viewer with stack trace inspection, built for Filament v3 to v5. Provides:

- Log table with searchable entries from `storage/logs`
- Stack trace inspection in slide-over modals
- Mail log preview for email logged entries
- Multiple filter types (log level tabs, date range, file)
- Dark mode ready
- Multilingual support (English, Arabic, German, Spanish, Persian, French, Hebrew, Italian, Portuguese)
- Copy log entries as formatted Markdown strings
- Visit `/logs` in your Filament panel after installation

## Rules

### Version Compatibility

Always use the correct package version for your Filament version:

| Package Version | Filament Version | PHP  |
|-----------------|------------------|------|
| `^2.x`          | v5               | ≥8.2 |
| `^1.x`          | v4               | ≥8.1 |
| `^0.x`          | v3               | ≥8.0 |

### Customizations

#### Authorization

Use the `->authorize()` method to control access to the Log Viewer page. You can pass a `Closure` that returns a boolean based on your authorization logic.

#### Navigation Registration

By default, the Log Viewer will be registered in the Filament sidebar navigation. You can disable this with `->registerNavigation(false)` if you want to link to it directly without showing it in the sidebar.

#### Navigation Customization

You can customize the navigation group, icon, label, sort order, and URL using the respective methods: `->navigationGroup()`, `->navigationIcon()`, `->navigationLabel()`, `->navigationSort()`, and `->navigationUrl()` respectively.

#### Polling Time

The `->pollingTime()` method allows you to set how often the log table should refresh to show new log entries. You can specify this in seconds (e.g., `'60s'`) or set it to `null` to disable polling.

### Production Security

1. **Disable Log Deletion**: Set `LOG_ENABLE_DELETE=false` in production to prevent accidental log deletion.

```php
// config/filament-log-viewer.php
'enable_delete' => env('LOG_ENABLE_DELETE', false),
```

Or in `.env`:
```
LOG_ENABLE_DELETE=false
```

2. **Authorization**: Always use `->authorize()` closure for access control - never leave it open to all users.

```php
FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->user()->is_admin);
```

### Performance

1. **File Size Limit**: Adjust `LOG_MAX_SIZE_KB` based on available server memory. Default is 2MB.

2. **Disable Polling**: Set `->pollingTime(null)` in production to reduce server load.

```php
->pollingTime(null) // Disable auto-refresh in production
```

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

FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->user()->is_admin)
    ->registerNavigation(true)
    ->navigationGroup('System')
    ->navigationIcon('heroicon-o-document-text')
    ->navigationLabel('Log Viewer')
    ->navigationSort(10)
    ->navigationUrl('/logs')
    ->pollingTime('60s');
```

### Authorization with Filament Shield

```php
FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->check() && auth()->user()->can('View:LogTable'));
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
    'copy_markdown_levels' => explode(',', env('LOG_COPY_MARKDOWN_LEVELS', 'error')),
];
```

### Copy as Markdown Configuration

Control which log levels show the "Copy as Markdown" button:

```php
// Only error logs (default)
LOG_COPY_MARKDOWN_LEVELS=error

// Multiple levels
LOG_COPY_MARKDOWN_LEVELS=error,mail,warning

// Or in config
'copy_markdown_levels' => ['error', 'mail'],
```

Available log levels: `error`, `warning`, `critical`, `alert`, `emergency`, `info`, `notice`, `debug`, `mail`

## Anti-patterns

- Using wrong package version for your Filament version - always check compatibility table
- Not disabling `enable_delete` in production - risks accidental log deletion
- Missing `authorize()` check - exposes logs to all users including customers
- Setting very large `max_log_file_size` without considering memory constraints
- Leaving polling enabled in production without considering server load
- Not configuring `copy_markdown_levels` for your use case - defaults to error only

## References

- Official Documentation: https://filamentphp.com/plugins/achyutn-log-viewer
- GitHub Repository: https://github.com/achyutkneupane/filament-log-viewer
- Laravel Boost: https://laravel.com/docs/boost
