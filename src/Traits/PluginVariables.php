<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait PluginVariables
{
    use EvaluatesClosures;

    public bool|Closure $authorized = true;

    public string|Closure|null $navigationGroup = null;

    public string|Closure $navigationIcon = 'heroicon-o-document-text';

    public string|Closure|null $navigationLabel = null;

    public int|Closure $navigationSort = 9999;

    public string|Closure $navigationUrl = '/logs';

    public string|null|Closure $pollingTime = null;

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorized);
    }

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup);
    }

    public function getNavigationIcon(): string
    {
        return $this->evaluate($this->navigationIcon);
    }

    public function getNavigationLabel(): ?string
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
