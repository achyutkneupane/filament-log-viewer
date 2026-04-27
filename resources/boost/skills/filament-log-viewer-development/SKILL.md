---
name: filament-log-viewer-development
description: Build and work with Filament Log Viewer plugin features, including plugin configuration, log parsing, table customization, and extensions.
---

# Filament Log Viewer Development

Use this skill when working with the Filament Log Viewer package - a Filament plugin to view and manage Laravel log files.

## Filament Compatibility

Ensure you use the correct package version for your Filament version:

| Package Version | Filament Version | PHP  |
|-----------------|------------------|------|
| `^2.x`          | v5               | ≥8.2 |
| `^1.x`          | v4               | ≥8.1 |
| `^0.x`          | v3               | ≥8.0 |

## Installation

```bash
composer require achyutn/filament-log-viewer
```

Register the plugin in your Filament panel:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

return $panel
    ->plugins([
        FilamentLogViewer::make(),
    ]);
```

## Configuration

### Plugin Options

Full Example of plugin registration with all available options:

```php
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

#### Authorization

Use the `->authorize()` method to control access to the Log Viewer page. You can pass a `Closure` that returns a boolean based on your authorization logic.

#### Navigation Registration

By default, the Log Viewer will be registered in the Filament sidebar navigation. You can disable this with `->registerNavigation(false)` if you want to link to it directly without showing it in the sidebar.

#### Navigation Customization

You can customize the navigation group, icon, label, sort order, and URL using the respective methods: `->navigationGroup()`, `->navigationIcon()`, `->navigationLabel()`, `->navigationSort()`, and `->navigationUrl()` respectively.

#### Polling Time

The `->pollingTime()` method allows you to set how often the log table should refresh to show new log entries. You can specify this in seconds (e.g., `'60s'`) or set it to `null` to disable polling.

### Config File

Publish the config:

```bash
php artisan vendor:publish --tag=filament-log-viewer-config
```

Then edit `config/filament-log-viewer.php`:

```php
return [
    'max_log_file_size' => env('LOG_MAX_SIZE_KB', 2048),
    'enable_delete' => env('LOG_ENABLE_DELETE', true),
];
```

Or use environment variables:

```
LOG_MAX_SIZE_KB=20480
LOG_ENABLE_DELETE=false
```
