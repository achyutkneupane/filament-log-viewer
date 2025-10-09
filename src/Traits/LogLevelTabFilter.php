<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Traits;

use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Resources\Concerns\HasTabs;
use Filament\Schemas\Components\Tabs\Tab;

trait LogLevelTabFilter
{
    use HasTabs;

    public string $unscopedLogLevel = 'all-logs';

    public function tabIsActive(string $tab): bool
    {
        if ($tab === $this->unscopedLogLevel) {
            return $this->activeTab === null || $this->activeTab === $this->unscopedLogLevel;
        }

        return $this->activeTab === $tab;
    }

    public function tableIsUnscoped(): bool
    {
        return in_array($this->activeTab, [null, $this->unscopedLogLevel], true);
    }

    /** @return array<string, mixed> */
    public function getTabs(): array
    {
        /** @var array<string, mixed> $all_logs */
        $all_logs = [
            $this->unscopedLogLevel => Tab::make(__('filament-log-viewer::log.levels.all'))
                ->id($this->unscopedLogLevel)
                ->badge(fn (): ?int => Log::getLogCount()),
        ];

        $exceptMail = array_filter(LogLevel::cases(), fn (LogLevel $level): bool => $level !== LogLevel::MAIL);

        /** @var array<string, mixed> $tabs */
        $tabs = collect($exceptMail)
            ->mapWithKeys(fn (LogLevel $level): array => [
                $level->value => Tab::make($level->getLabel())
                    ->id($level->value)
                    ->badge(
                        fn (): ?int => Log::getLogCount($level->value),
                    )
                    ->badgeColor($level->getColor()),
            ])->toArray();

        if (Log::getLogCount('mail') > 0) {
            /** @var int<1,max> $mailCount */
            $mailCount = Log::getLogCount('mail');

            $tabs['mail'] = Tab::make('Mail')
                ->label(__('filament-log-viewer::log.levels.mail'))
                ->id('mail')
                ->badge($mailCount)
                ->badgeColor(LogLevel::MAIL->getColor());
        }

        return array_merge($all_logs, $tabs);
    }
}
