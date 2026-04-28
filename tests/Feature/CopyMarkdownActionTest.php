<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature;

use AchyutN\FilamentLogViewer\Actions\CopyMarkdownAction;
use AchyutN\FilamentLogViewer\LogTable;
use AchyutN\FilamentLogViewer\Model\Log;
use Filament\Actions\Action;
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

it('hides copy as markdown action when config set to false', function () {
    Config::set('filament-log-viewer.enable_copy_markdown', false);

    livewire(LogTable::class)
        ->assertTableActionExists('copy_markdown', fn (CopyMarkdownAction $action) => ! $action->isVisible());
});

it('copies markdown to clipboard and shows notification', function () {
    $record = Log::getRows()[0];
    
    livewire(LogTable::class)
        ->callTableAction('copy_markdown', $record)
        ->assertSuccessful()
        ->assertNotified(__('filament-log-viewer::log.table.actions.copy_markdown.success'));
});

it('generates correct markdown for various log entries', function () {
    $action = new CopyMarkdownAction('copy_markdown');
    $reflection = new \ReflectionClass($action);
    $method = $reflection->getMethod('generateMarkdown');
    $method->setAccessible(true);

    // Case 1: Standard error log
    $record = [
        'date' => '2024-08-06 20:15:00',
        'env' => 'local',
        'log_level' => \AchyutN\FilamentLogViewer\Enums\LogLevel::ERROR,
        'file' => 'laravel.log',
        'message' => 'Sample log',
        'description' => 'Some description',
        'context' => ['user_id' => 1],
        'has_stack' => true,
        'raw_stack' => 'stack trace content',
        'mail' => null,
    ];

    $markdown = $method->invoke($action, $record);

    expect($markdown)->toContain('# [2024-08-06 20:15:00] local.ERROR');
    expect($markdown)->toContain('**'.__('filament-log-viewer::log.table.actions.copy_markdown.headers.file').':** `laravel.log`');
    expect($markdown)->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.message')."\n".'Sample log');
    expect($markdown)->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.description')."\n".'`Some description`');
    expect($markdown)->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.context')."\n".'```json'."\n".'{'."\n".'    "user_id": 1'."\n".'}');
    expect($markdown)->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.stack_trace')."\n".'```text'."\n".'stack trace content');

    // Case 2: Minimal log
    $record = [
        'date' => '2024-08-06 20:15:00',
        'env' => 'local',
        'log_level' => \AchyutN\FilamentLogViewer\Enums\LogLevel::INFO,
        'file' => 'laravel.log',
        'message' => 'Simple log',
        'description' => null,
        'context' => null,
        'has_stack' => false,
        'raw_stack' => null,
        'mail' => null,
    ];

    $markdown = $method->invoke($action, $record);

    expect($markdown)->not->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.description'));
    expect($markdown)->not->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.context'));
    expect($markdown)->not->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.stack_trace'));

    // Case 3: Mail log
    $record = [
        'date' => '2024-08-06 20:15:00',
        'env' => 'local',
        'log_level' => \AchyutN\FilamentLogViewer\Enums\LogLevel::MAIL,
        'file' => 'laravel.log',
        'message' => 'Mail log',
        'description' => null,
        'context' => null,
        'has_stack' => false,
        'raw_stack' => null,
        'mail' => ['subject' => 'Test'],
    ];

    $markdown = $method->invoke($action, $record);

    expect($markdown)->toContain('## '.__('filament-log-viewer::log.table.actions.copy_markdown.headers.mail_details')."\n".'```json'."\n".'{'."\n".'    "subject": "Test"'."\n".'}');
});
