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

    public string|Closure $navigationSort = '9999';

    public string|Closure $navigationUrl = '/logs';

    public static function make(): self
    {
        $plugin = app(self::class);

        $plugin->authorize(fn (): bool => auth()->check() && self::isAuthorized());
        $plugin->navigationGroup(fn (): string => self::getNavigationGroup());
        $plugin->navigationIcon(fn (): string => self::getNavigationIcon());
        $plugin->navigationLabel(fn (): string => self::getNavigationLabel());
        $plugin->navigationSort(fn (): string => self::getNavigationSort());
        $plugin->navigationUrl(fn (): string => self::getNavigationUrl());

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

    public function navigationSort(string|Closure $sort): self
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function navigationUrl(string|Closure $url): self
    {
        $this->navigationUrl = $url;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return auth()->check() && $this->evaluate($this->authorized);
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

    public function getNavigationSort(): string
    {
        return $this->evaluate($this->navigationSort);
    }

    public function getNavigationUrl(): string
    {
        return $this->evaluate($this->navigationUrl);
    }
}
