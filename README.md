# Filament Log Viewer

![Filament Log Viewer](https://banners.beyondco.de/Filament%20Log%20Viewer.png?theme=light&packageManager=composer+require&packageName=achyutn%2Ffilament-log-viewer&pattern=hideout&style=style_2&description=A+Filament+package+to+view+and+manage+Laravel+logs.&md=1&showWatermark=0&fontSize=175px&images=document-report)

![Packagist Version](https://img.shields.io/packagist/v/achyutn/filament-log-viewer?label=Latest%20Version)
![Packagist Downloads](https://img.shields.io/packagist/dt/achyutn/filament-log-viewer?label=Packagist%20Downloads)
![Packagist Stars](https://img.shields.io/packagist/stars/achyutn/filament-log-viewer?label=Stars)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=achyutkneupane_filament-log-viewer&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=achyutkneupane_filament-log-viewer)
[![Lint & Test PR](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/prlint.yml/badge.svg)](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/prlint.yml)
[![Bump version](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/tagrelease.yml/badge.svg)](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/tagrelease.yml)

A Filament plugin to read and display Laravel log files in a clean, searchable table with stack traces and filtering.

Refer to [version compatibility table](#filament-compatibility) below to ensure you are using the correct version of this package with your Filament installation.

## Installation

```bash
composer require achyutn/filament-log-viewer
```

Register the plugin inside your Filament panel:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

return $panel
    ->plugins([
        FilamentLogViewer::make(),
    ]);
```

### Laravel Boost Integration

This package provides a Laravel Boost skill for AI-assisted development. When you install Laravel Boost in your project, the skill will be automatically discovered and made available to AI agents.

See [Laravel Boost documentation](https://laravel.com/docs/boost) for more details.


## Usage

After installation, visit `/logs` in your Filament panel. You will see a table of log entries.

### Configuration

You can configure the maximum file size limit for log files to be loaded and displayed. This helps prevent performance
issues with very large log files.

The default file size limit is set to `2 MB`. To override these settings, you can set environment variables in your `.env` file:

```
LOG_MAX_SIZE_KB=20480
LOG_ENABLE_DELETE=false
LOG_ENABLE_COPY_MARKDOWN=false
LOG_COPY_MARKDOWN_LEVELS=error,warning
```

- Set `LOG_MAX_SIZE_KB` to the maximum log file size in kilobytes (e.g., `20480` for 20 MB).
- Set `LOG_ENABLE_DELETE=false` in production to disable the **Clear Logs** button and protect log files from accidental deletion.
- Set `LOG_ENABLE_COPY_MARKDOWN=false` to disable the **Copy as Markdown** button.
- Set `LOG_COPY_MARKDOWN_LEVELS` to comma-separated list of log levels that show the copy button (e.g., `error,warning` or just `error`).

Or, you can publish the configuration file and update the `max_log_file_size` value as needed:

```bash
php artisan vendor:publish --tag=filament-log-viewer-config
```

Then, in your published `config/filament-log-viewer.php` file:

```php
return [
    // Set max file size to 20 MB
    'max_log_file_size' => env('LOG_MAX_SIZE_KB', 20480),

    // Disable deleting logs from the UI
    'enable_delete' => env('LOG_ENABLE_DELETE', false),

    // Disable copying logs as markdown
    'enable_copy_markdown' => env('LOG_ENABLE_COPY_MARKDOWN', true),

    // Show copy button for these log levels (comma-separated)
    'copy_markdown_levels' => explode(',', env('LOG_COPY_MARKDOWN_LEVELS', 'error')),
];
```

### Table Columns

- **Log Level** – Badge with color mapped from log level
- **Environment** *(Toggleable)* – Application environment (`local`, `production`, etc.)
- **File** *(Toggleable)* – Log file name (e.g., `laravel.log`)
- **Message** – Short summary of the log
- **Occurred** – Human-readable date/time
- **Copy as Markdown** – Action to copy log details in a clean Markdown format.

![Table Preview](https://hamrocdn.com/75qlRGETrri6)

Click the view action to inspect stack traces.

![Stack Tracing](https://hamrocdn.com/wPKsaqoXH5H4)

### Mail Preview

If your logs contain mail messages, you can preview them directly from the table. You can click on `Mail` tab which is visible only if mail are present.

![Mail Preview](https://hamrocdn.com/hrr5B2GpKSke)

### Filters

#### Log Levels

You can filter the logs according to log level. The filters are available as tabs above the table:

![Log level Filters](https://hamrocdn.com/wpzpwsrvZaxp)

#### Date

You can filter logs by date using the date picker in the top right corner of the table. This allows you to select a specific date range to view logs.

![Date Filter](https://hamrocdn.com/am_RAj2VQHiG)

#### Toggle Columns

You can toggle the visibility of the **Environment** and **File** columns by clicking the eye icon in the top right corner of the table.

![Toggle Columns](https://hamrocdn.com/q4eZM97btUf2)

### Authorization

You can make a boolean check to authorize the log viewer. It will be helpful if you want to show/hide the log viewer for certain cases.

#### Example

You simply return a `boolean` or `Closure` which evaluates to a `boolean`.

```php
FilamentLogViewer::make()
    ->authorize(true);

// or

FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->user()->is_admin);
```

If you are using [filament-sheild](https://github.com/bezhanSalleh/filament-shield) or any other external services for authorization, you can use a `Closure` with permission check:

```php
FilamentLogViewer::make()
    ->authorize(fn (): bool => auth()->check() && auth()->user()->can('View:LogTable'));
```

## Extending

You can customize navigation label, icon, sort, etc. using:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()
    ->authorize(fn () => auth()->check())
    ->registerNavigation(true)
    ->navigationGroup('System')
    ->navigationIcon('heroicon-o-document-text')
    ->navigationLabel('Log Viewer')
    ->navigationSort(10)
    ->navigationUrl('/logs')
    ->pollingTime(null); // Set to null to disable polling
```

Set `->registerNavigation(false)` if you want to hide Log Viewer from the sidebar while still linking to it directly (for example, from a custom dashboard action).

## Localization

Filament Log Viewer includes built-in translations for:

- [English](src/resources/lang/en/log.php)
- [Arabic](src/resources/lang/ar/log.php)
- [German](src/resources/lang/de/log.php)
- [Spanish](src/resources/lang/es/log.php)
- [Persian](src/resources/lang/fa/log.php)
- [French](src/resources/lang/fr/log.php)
- [Hebrew](src/resources/lang/he/log.php)
- [Italian](src/resources/lang/it/log.php)
- [Portuguese (Portugal)](src/resources/lang/pt/log.php)
- [Portuguese (Brazil)](src/resources/lang/pt_BR/log.php)

Translations are applied automatically based on your application's current locale.

> Missing your language? Feel free to [submit a PR](https://github.com/achyutkneupane/filament-log-viewer/pulls) to add it!

## Filament Compatibility

|                                     Version                                      | Filament Version |
|:--------------------------------------------------------------------------------:|------------------|
| [`^2.x`](https://github.com/achyutkneupane/filament-log-viewer/tree/filament-v5) | Filament v5      |
| [`^1.x`](https://github.com/achyutkneupane/filament-log-viewer/tree/filament-v4) | Filament v4      |
| [`^0.x`](https://github.com/achyutkneupane/filament-log-viewer/tree/filament-v3) | Filament v3      |

Make sure you're using the appropriate version of this package for your Filament installation.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Changelog

See the [CHANGELOG](CHANGELOG.md) for details on changes made in each version.

## Contributing

Contributions are welcome! Please create a pull request or open an issue if you find any bugs or have feature requests.

## Support

If you find this package useful, please consider starring the repository on GitHub to show your support.
