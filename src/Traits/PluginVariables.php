<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use UnitEnum;

trait PluginVariables
{
    use EvaluatesClosures;

    public bool|Closure $authorized = true;

    public string|UnitEnum|Closure|null $navigationGroup = null;

    public string|Closure $navigationIcon = 'heroicon-o-document-text';

    public string|Closure|null $navigationLabel = null;

    public int|Closure $navigationSort = 9999;

    public string|Closure $navigationUrl = '/logs';

    public string|null|Closure $pollingTime = null;

    public function isAuthorized(): bool
    {
        return (bool) $this->evaluate($this->authorized);
    }

    public function getNavigationGroup(): string|UnitEnum|null
    {
        /** @var string|UnitEnum|null */
        return $this->evaluate($this->navigationGroup);
    }

    public function getNavigationIcon(): string
    {
        /** @phpstan-var string */
        return $this->evaluate($this->navigationIcon);
    }

    public function getNavigationLabel(): string
    {
        /** @phpstan-var string */
        return $this->evaluate($this->navigationLabel) ?? '';
    }

    public function getNavigationSort(): int
    {
        /** @var int */
        return $this->evaluate($this->navigationSort);
    }

    public function getNavigationUrl(): string
    {
        /** @var string */
        return $this->evaluate($this->navigationUrl);
    }

    public function getPollingTime(): ?string
    {
        /** @var string|null */
        return $this->evaluate($this->pollingTime);
    }
}
