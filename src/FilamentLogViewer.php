<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

final class FilamentLogViewer implements Plugin
{
    use EvaluatesClosures;

    public bool|Closure $authorized = true;

    public string|Closure $navigationGroup = 'System';

    public string|Closure $navigationIcon = 'heroicon-o-document-text';

    public string|Closure $navigationLabel = 'Log Viewer';

    public int|Closure $navigationSort = 9999;

    public string|Closure $navigationUrl = '/logs';

    public string|null|Closure $pollingTime = null;

    public static function make(): self
    {
        $plugin = app(self::class);

        $plugin->authorize($plugin->isAuthorized());
        $plugin->navigationGroup($plugin->getNavigationGroup());
        $plugin->navigationIcon($plugin->getNavigationIcon());
        $plugin->navigationLabel($plugin->getNavigationLabel());
        $plugin->navigationSort($plugin->getNavigationSort());
        $plugin->navigationUrl($plugin->getNavigationUrl());
        $plugin->pollingTime($plugin->getPollingTime());

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
        //
    }

    public function authorize(bool|Closure $callback): self
    {
        $this->authorized = $callback;

        return $this;
    }

    public function navigationGroup(string|Closure $group): self
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

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorized);
    }

    public function getNavigationGroup(): string
    {
        return $this->evaluate($this->navigationGroup);
    }

    public function getNavigationIcon(): string
    {
        return $this->evaluate($this->navigationIcon);
    }

    public function getNavigationLabel(): string
    {
        return $this->evaluate($this->navigationLabel);
    }

    public function getNavigationSort(): int
    {
        return $this->evaluate($this->navigationSort);
    }

    public function getNavigationUrl(): string
    {
        return $this->evaluate($this->navigationUrl);
    }

    public function getPollingTime(): ?string
    {
        return $this->evaluate($this->pollingTime);
    }
}
