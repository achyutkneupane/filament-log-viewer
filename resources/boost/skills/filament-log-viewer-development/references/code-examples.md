---
name: filament-log-viewer-code-examples
description: Copy/paste examples for custom parsers, schemas, and pages in the achyutn/filament-log-viewer package. Provider examples live in references/providers.
license: MIT
tags:
  - filament
  - logs
  - extensibility
metadata:
  author: Achyut Neupane
---

# Filament Log Viewer Code Examples

## Context

These examples customize the non-provider contracts of `achyutn/filament-log-viewer`. Every default implementation is non-final with `protected` extension points, so you can extend a default and override a single behavior instead of reimplementing a contract. Provider examples live in `references/providers/`.

> Parsers and providers exchange rows as the `LogRow` array shape (`date`, `env`, `log_level`, `message`, `description`, `context`, `raw_stack`, `has_stack`, `mail`, `file`). The table columns and modal schemas read those exact keys. For a fully custom DTO/object shape with your own table, see `references/custom-viewer.md`.

## Examples

### 1) LogParser — custom parser

```php
<?php

declare(strict_types=1);

namespace App\LogParsers;

use AchyutN\FilamentLogViewer\Parsers\FileLogParser;

final class CustomLogParser extends FileLogParser
{
    public function isMailEntry(string $message): bool
    {
        // Custom mail detection for your log format.
        return str_contains($message, 'X-Mailer:');
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->parserClass(CustomLogParser::class);
```

### 2) MailParser — custom mail parser

```php
<?php

declare(strict_types=1);

namespace App\LogParsers;

use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;

final class CustomMailParser extends DefaultMailParser
{
    public function isMailStack(?string $logStack): bool
    {
        if (! $logStack) {
            return false;
        }

        return str_contains($logStack, 'X-Mailer:');
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->mailParserClass(CustomMailParser::class);
```

### 3) StackTraceParser — custom stack trace parser

```php
<?php

declare(strict_types=1);

namespace App\LogParsers;

use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;

final class CustomStackTraceParser extends DefaultStackTraceParser
{
    public function hasStack(string $raw): bool
    {
        return str_contains($raw, 'Trace:') || parent::hasStack($raw);
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->stackTraceParserClass(CustomStackTraceParser::class);
```

### 4) LogViewerPage — custom page

```php
<?php

declare(strict_types=1);

namespace App\LogTables;

use AchyutN\FilamentLogViewer\LogTable;
use Filament\Actions\Action;

final class CustomLogTable extends LogTable
{
    protected function getHeaderActions(): array
    {
        // Swap or extend the header actions.
        return [...parent::getHeaderActions(), Action::make('export')];
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->pageClass(CustomLogTable::class);
```

### 5) LogTableSchemaInterface — custom table columns

```php
<?php

declare(strict_types=1);

namespace App\LogSchemas;

use AchyutN\FilamentLogViewer\Contracts\Schema\LogTableSchemaInterface;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

final class MyLogTableSchema implements LogTableSchemaInterface
{
    /** @return array<Column> */
    public function getColumns(): array
    {
        return [
            TextColumn::make('message')->label('Summary')->wrap(),
            TextColumn::make('date')->label('Occurred')->since(),
        ];
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->tableSchemaClass(MyLogTableSchema::class);
```

### 6) LogEntrySchemaInterface — custom modal schema

```php
<?php

declare(strict_types=1);

namespace App\LogSchemas;

use AchyutN\FilamentLogViewer\Contracts\Schema\LogEntrySchemaInterface;
use Filament\Schemas\Schema;

final class MyErrorLogSchema implements LogEntrySchemaInterface
{
    public function configure(Schema $schema): Schema
    {
        return $schema
            ->key('error-log')
            ->schema([
                // Your infolist components.
            ]);
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->errorSchemaClass(MyErrorLogSchema::class);
```

## Anti-patterns / Gotchas

- Extend the non-final defaults and override `protected` hooks instead of reimplementing a contract from scratch when you only need one change.
- Overrides are resolved through Laravel's service container, so constructor dependencies are auto-wired; keep constructor signatures compatible.
- The plugin is a container singleton, so overrides apply app-wide rather than per-panel.

## References

- Contracts: `src/Contracts/*`
- Parsers: `src/Parsers/*`
- Schemas: `src/Schema/*`
- Page: `src/LogTable.php`
- Skill: `resources/boost/skills/filament-log-viewer-development/SKILL.md`
- Provider examples: `references/providers/*`
