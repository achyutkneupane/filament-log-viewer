# Filament Log Viewer

A Filament plugin to read and display Laravel log files in a clean, searchable table with stack traces and filtering.

![Table Preview](https://hamrocdn.com/4WCWYgw7EPjB)

![Stack Tracing](https://hamrocdn.com/ZrA8W53Nx3CO)

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

Click the view action to inspect stack traces.

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
    ->navigationUrl('/logs');
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Changelog

See the [CHANGELOG](CHANGELOG.md) for details on changes made in each version.

## Contributing

Contributions are welcome! Please create a pull request or open an issue if you find any bugs or have feature requests.

## Support

If you find this package useful, please consider starring the repository on GitHub to show your support.
