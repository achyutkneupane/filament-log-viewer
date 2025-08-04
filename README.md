# Filament Log Viewer

![Filament Log Viewer](https://banners.beyondco.de/Filament%20Log%20Viewer.png?theme=light&packageManager=composer+require&packageName=achyutn%2Ffilament-log-viewer&pattern=hideout&style=style_2&description=A+Filament+package+to+view+and+manage+Laravel+logs.&md=1&showWatermark=0&fontSize=175px&images=document-report)

![Packagist Version](https://img.shields.io/packagist/v/achyutn/filament-log-viewer?label=Latest%20Version)
![Packagist Downloads](https://img.shields.io/packagist/dt/achyutn/filament-log-viewer?label=Packagist%20Downloads)
![Packagist Stars](https://img.shields.io/packagist/stars/achyutn/filament-log-viewer?label=Stars)
[![Bump version](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/tagrelease.yml/badge.svg)](https://github.com/achyutkneupane/filament-log-viewer/actions/workflows/tagrelease.yml)

A Filament plugin to read and display Laravel log files in a clean, searchable table with stack traces and filtering.

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

## Usage

After installation, visit `/logs` in your Filament panel. You will see a table of log entries.

### Table Columns

- **Log Level** – Badge with color mapped from log level
- **Environment** *(Toggleable)* – Application environment (`local`, `production`, etc.)
- **File** *(Toggleable)* – Log file name (e.g., `laravel.log`)
- **Message** – Short summary of the log
- **Occurred** – Human-readable date/time

![Table Preview](https://hamrocdn.com/4WCWYgw7EPjB)

Click the view action to inspect stack traces.

![Stack Tracing](https://hamrocdn.com/ZrA8W53Nx3CO)

### Filters

#### Log Levels

You can filter the logs according to log level. The filters are available as tabs above the table:

![Table Filters](https://hamrocdn.com/175hFkEVdrDl)

#### Date

You can filter logs by date using the date picker in the top right corner of the table. This allows you to select a specific date range to view logs.

![Date Filter](https://hamrocdn.com/q9sILZZYuxlN)

## Extending

You can customize navigation label, icon, sort, etc. using:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()
    ->authorize(fn () => auth()->check())
    ->navigationGroup('System')
    ->navigationIcon('heroicon-o-document-text')
    ->navigationLabel('Log Viewer')
    ->navigationSort(10)
    ->navigationUrl('/logs')
    ->pollingTime(null); // Set to null to disable polling
```

## Filament Compatibility

| Version             | Filament Version |
|---------------------|------------------|
| `^0.x`              | Filament v3      |
| `^1.0`              | Filament v4      |

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Changelog

See the [CHANGELOG](CHANGELOG.md) for details on changes made in each version.

## Contributing

Contributions are welcome! Please create a pull request or open an issue if you find any bugs or have feature requests.

## Support

If you find this package useful, please consider starring the repository on GitHub to show your support.
