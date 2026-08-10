<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use UnitEnum;

trait HasLogViewerNavigation
{
    /** @throws Exception */
    public static function getNavigationLabel(): string
    {
        return self::getPlugin()->getNavigationLabel();
    }

    /** @throws Exception */
    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return self::getPlugin()->getNavigationGroup();
    }

    /** @throws Exception */
    public static function getNavigationSort(): int
    {
        return self::getPlugin()->getNavigationSort();
    }

    /** @throws Exception */
    public static function getSlug(?Panel $panel = null): string
    {
        return mb_ltrim(
            self::getPlugin($panel)->getNavigationUrl(),
            '/'
        );
    }

    /** @throws Exception */
    public static function getNavigationIcon(): string
    {
        return self::getPlugin()->getNavigationIcon();
    }

    /** @throws Exception */
    public static function shouldRegisterNavigation(): bool
    {
        return self::getPlugin()->shouldRegisterNavigation();
    }

    /** @throws Exception */
    public static function canAccess(): bool
    {
        return self::getPlugin()->isAuthorized();
    }

    public function getHeading(): string
    {
        return __('filament-log-viewer::log.navigation.heading');
    }

    public function getSubheading(): string
    {
        return __('filament-log-viewer::log.navigation.subheading');
    }

    public function getTitle(): string
    {
        return __('filament-log-viewer::log.navigation.title');
    }

    /**
     * @throws Exception
     */
    protected static function getPlugin(?Panel $panel = null): FilamentLogViewer
    {
        $panel ??= Filament::getCurrentPanel() ?? Filament::getDefaultPanel();
        $logViewer = FilamentLogViewer::make();

        if ($panel->hasPlugin($logViewer->getId())) {
            /** @var FilamentLogViewer */
            return $panel->getPlugin($logViewer->getId());
        }

        return $logViewer;
    }
}
