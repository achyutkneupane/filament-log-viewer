<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature;

use AchyutN\FilamentLogViewer\Actions\CopyMarkdownAction;
use AchyutN\FilamentLogViewer\LogTable;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Config;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->initializeLogs();
});

it('can render copy as markdown action', function () {
    livewire(LogTable::class)
        ->assertTableActionExists('copy_markdown', function (CopyMarkdownAction $action) {
            return $action->getLabel() === __('filament-log-viewer::log.table.actions.copy_markdown.label') &&
                $action->getIcon() === Heroicon::DocumentDuplicate &&
                $action->getColor() === Color::Gray;
        });
});

it('hides copy as markdown action for mail logs', function () {
    livewire(LogTable::class)
        ->assertTableActionExists('copy_markdown', fn (CopyMarkdownAction $action) => $action->isHidden());
});

it('hides copy as markdown action when config set to false', function () {
    Config::set('filament-log-viewer.enable_copy_markdown', false);

    livewire(LogTable::class)
        ->assertTableActionExists('copy_markdown', fn (CopyMarkdownAction $action) => ! $action->isVisible());
});
