<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer;

use AchyutN\FilamentLogViewer\Traits\PluginVariables;
use BackedEnum;
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
        $plugin->registerNavigation($plugin->shouldRegisterNavigation());

        $navigationGroup = $plugin->getNavigationGroup();
        $navigationLabel = $plugin->getNavigationLabel();

        if ($navigationGroup) {
            $plugin->navigationGroup($navigationGroup);
        } else {
            $plugin->navigationGroup(fn (): string|array => __('filament-log-viewer::log.navigation.group'));
        }

        if ($navigationLabel) {
            $plugin->navigationLabel($navigationLabel);
        } else {
            $plugin->navigationLabel(fn (): string|array => __('filament-log-viewer::log.navigation.label'));
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
                $this->getPageClass(),
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

    public function navigationIcon(string|BackedEnum|Closure $icon): self
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

    public function pageClass(string|Closure|null $class): self
    {
        $this->pageClass = $class;

        return $this;
    }

    public function providerClass(string|Closure|null $class): self
    {
        $this->providerClass = $class;

        return $this;
    }

    public function parserClass(string|Closure|null $class): self
    {
        $this->parserClass = $class;

        return $this;
    }

    public function mailParserClass(string|Closure|null $class): self
    {
        $this->mailParserClass = $class;

        return $this;
    }

    public function stackTraceParserClass(string|Closure|null $class): self
    {
        $this->stackTraceParserClass = $class;

        return $this;
    }

    public function tableSchemaClass(string|Closure|null $class): self
    {
        $this->tableSchemaClass = $class;

        return $this;
    }

    public function errorSchemaClass(string|Closure|null $class): self
    {
        $this->errorSchemaClass = $class;

        return $this;
    }

    public function jsonSchemaClass(string|Closure|null $class): self
    {
        $this->jsonSchemaClass = $class;

        return $this;
    }

    public function mailSchemaClass(string|Closure|null $class): self
    {
        $this->mailSchemaClass = $class;

        return $this;
    }

    public function copyMarkdownActionClass(string|Closure|null $class): self
    {
        $this->copyMarkdownActionClass = $class;

        return $this;
    }

    public function dateRangeFilterClass(string|Closure|null $class): self
    {
        $this->dateRangeFilterClass = $class;

        return $this;
    }

    public function fileFilterClass(string|Closure|null $class): self
    {
        $this->fileFilterClass = $class;

        return $this;
    }

    public function pollingTime(string|null|Closure $time): self
    {
        $this->pollingTime = $time;

        return $this;
    }

    public function registerNavigation(bool $registerNavigation = true): self
    {
        $this->registerNavigation = $registerNavigation;

        return $this;
    }
}
