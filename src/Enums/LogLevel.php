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

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALERT => 'Alert',
            self::CRITICAL => 'Critical',
            self::DEBUG => 'Debug',
            self::EMERGENCY => 'Emergency',
            self::ERROR => 'Error',
            self::INFO => 'Info',
            self::NOTICE => 'Notice',
            self::WARNING => 'Warning',
        };
    }

    public function getColor(): string|array|null
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
        };
    }
}
