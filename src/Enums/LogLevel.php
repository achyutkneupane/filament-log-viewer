<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LogLevel: string implements HasColor, HasLabel
{
    case ALERT = 'alert';
    case CRITICAL = 'critical';
    case DEBUG = 'debug';
    case EMERGENCY = 'emergency';
    case ERROR = 'error';
    case INFO = 'info';
    case NOTICE = 'notice';
    case WARNING = 'warning';
    case MAIL = 'mail';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALERT => __('filament-log-viewer::log.levels.alert'),
            self::CRITICAL => __('filament-log-viewer::log.levels.critical'),
            self::DEBUG => __('filament-log-viewer::log.levels.debug'),
            self::EMERGENCY => __('filament-log-viewer::log.levels.emergency'),
            self::ERROR => __('filament-log-viewer::log.levels.error'),
            self::INFO => __('filament-log-viewer::log.levels.info'),
            self::NOTICE => __('filament-log-viewer::log.levels.notice'),
            self::WARNING => __('filament-log-viewer::log.levels.warning'),
            self::MAIL => __('filament-log-viewer::log.levels.mail'),
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::ALERT => Color::hex('#FF0000'),
            self::CRITICAL => Color::hex('#D32F2F'),
            self::DEBUG => Color::hex('#90CAF9'),
            self::EMERGENCY => Color::hex('#B71C1C'),
            self::ERROR => Color::hex('#E53935'),
            self::INFO => Color::hex('#2196F3'),
            self::NOTICE => Color::hex('#4CAF50'),
            self::WARNING => Color::hex('#FFC107'),
            self::MAIL => Color::hex('#9C27B0'),
        };
    }
}
