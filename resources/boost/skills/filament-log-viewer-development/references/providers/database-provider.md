---
name: filament-log-viewer-database-provider
description: Copy/paste example of a deletable database-backed LogProvider implementing CanDeleteLogs so the clear buttons operate on stored log rows.
license: MIT
tags:
  - filament
  - logs
  - extensibility
metadata:
  author: Achyut Neupane
---

# Database Log Provider Example

## Context

A provider that can delete logs (e.g. a database-backed store) implements `LogProvider, CanDeleteLogs`. The **Clear Logs** and per-file clear buttons render and call `deleteAll()` / `deleteFile()`.

> **What is "file" in a database provider?** The viewer's contracts and UI are keyed on a `file` field, but for a database-backed store that is really the **source/channel identifier** of each row (e.g. `laravel`, `scheduler`, `mail`). `CanDeleteLogs::deleteFile(string $file)` is named for the file-based provider, yet it operates on whatever source key you map into the `file` field — `deleteFile('laravel')` deletes every stored row for that source. Map the DB column into the required `LogRow['file']` field so the table's File column, file filter, and per-file clear dropdown all work.

## Example

```php
<?php

declare(strict_types=1);

namespace App\LogProviders;

use AchyutN\FilamentLogViewer\Contracts\CanDeleteLogs;
use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use App\Models\LogEntry;

final class DatabaseLogProvider implements LogProvider, CanDeleteLogs
{
    public function getRows(bool $refresh = false): array
    {
        // Map stored rows into the LogRow array shape the table expects.
        // 'file' here carries the source/channel identifier of each row.
        return LogEntry::query()
            ->get()
            ->map(fn (LogEntry $entry): array => [
                'date' => $entry->date,
                'env' => $entry->env,
                'log_level' => $entry->level,
                'message' => $entry->message,
                'description' => $entry->description,
                'context' => $entry->context,
                'raw_stack' => $entry->raw_stack,
                'has_stack' => $entry->has_stack,
                'mail' => null,
                'file' => $entry->channel,
            ])
            ->all();
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
        // $file is the source/channel key per the CanDeleteLogs contract.
        LogEntry::query()->where('channel', $file)->delete();
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->providerClass(DatabaseLogProvider::class);
```

## Gotchas

- `deleteFile(string $file)` receives the same key returned by `getFilesForFilter()` — keep both aligned on your source identifier.
- Every row must match the `LogRow` array shape (`date`, `env`, `log_level`, `message`, `description`, `context`, `raw_stack`, `has_stack`, `mail`, `file`) for the default table to render it.
- Constructor dependencies of your provider are auto-wired by Laravel's service container.
- The plugin is a container singleton, so the provider applies app-wide rather than per-panel.

## References

- Contract: `src/Contracts/LogProvider.php`
- Capability: `src/Contracts/CanDeleteLogs.php`
- Default: `src/Providers/LocalLogProvider.php`
- Skill: `resources/boost/skills/filament-log-viewer-development/SKILL.md`
