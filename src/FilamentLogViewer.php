<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Traits\PluginVariables;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use UnitEnum;

final class FilamentLogViewer implements Plugin
{
    use PluginVariables;

    public static function make(): self
    {
        $plugin = app(self::class);

        $plugin->authorize($plugin->isAuthorized());
        $plugin->navigationIcon($plugin->getNavigationIcon());
        $plugin->navigationSort($plugin->getNavigationSort());
        $plugin->navigationUrl($plugin->getNavigationUrl());
        $plugin->pollingTime($plugin->getPollingTime());

        $navigationGroup = $plugin->getNavigationGroup();
        $navigationLabel = $plugin->getNavigationLabel();

        if ($navigationGroup) {
            $plugin->navigationGroup($navigationGroup);
        } else {
            $plugin->navigationGroup(__('filament-log-viewer::log.navigation.group'));
        }

        if ($navigationLabel) {
            $plugin->navigationLabel($navigationLabel);
        } else {
            $plugin->navigationLabel(__('filament-log-viewer::log.navigation.label'));
        }

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-log-viewer';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                LogTable::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // This plugin doesn't require boot-time logic for now.
    }

    public function authorize(bool|Closure $callback): self
    {
        $this->authorized = $callback;

        return $this;
    }

    public function navigationGroup(string|UnitEnum|Closure $group): self
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string|Closure $icon): self
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationLabel(string|Closure $label): self
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationSort(int|Closure $sort): self
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function navigationUrl(string|Closure $url): self
    {
        $this->navigationUrl = $url;

        return $this;
    }

    public function pollingTime(string|null|Closure $time): self
    {
        $this->pollingTime = $time;

        return $this;
    }
}
