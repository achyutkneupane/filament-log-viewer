---
name: filament-log-viewer-read-only-provider
description: Copy/paste example of a read-only LogProvider (cloud reader, Pail tail, or API stream) that omits CanDeleteLogs so the clear buttons stay hidden.
license: MIT
tags:
  - filament
  - logs
  - extensibility
metadata:
  author: Achyut Neupane
---

# Read-Only Log Provider Example

## Context

A provider that only reads logs (e.g. a cloud reader, a Pail tail, or an API stream) should implement `LogProvider` and omit `CanDeleteLogs`. The **Clear Logs** and per-file clear buttons are then hidden automatically — no misleading delete actions.

## Example

```php
<?php

declare(strict_types=1);

namespace App\LogProviders;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;

final class CloudLogProvider implements LogProvider
{
    public function getRows(bool $refresh = false): array
    {
        // Fetch rows from your API or stream.
        return [];
    }

    public function getLogsByLevel(string $level): array
    {
        return $this->getRows();
    }

    public function getCount(string $level = 'all-logs'): ?int
    {
        return count($this->getRows()) ?: null;
    }

    public function getFiles(): array
    {
        return ['cloud.log'];
    }

    public function getFilesForFilter(): array
    {
        return ['cloud.log' => 'cloud.log'];
    }

    public function getStackFromRaw(string $rawStack): array
    {
        return [];
    }
}
```

Register it:

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

FilamentLogViewer::make()->providerClass(CloudLogProvider::class);
```

## Gotchas

- Do not implement `CanDeleteLogs` on a read-only provider — it would show misleading clear buttons that cannot delete anything.
- Constructor dependencies of your provider are auto-wired by Laravel's service container.
- The plugin is a container singleton, so the provider applies app-wide rather than per-panel.

## References

- Contract: `src/Contracts/LogProvider.php`
- Default: `src/Providers/LocalLogProvider.php`
- Skill: `resources/boost/skills/filament-log-viewer-development/SKILL.md`
