<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature;

use AchyutN\FilamentLogViewer\LogTable;
use AchyutN\FilamentLogViewer\Parsers\DefaultMailParser;
use AchyutN\FilamentLogViewer\Parsers\DefaultStackTraceParser;
use AchyutN\FilamentLogViewer\Parsers\FileLogParser;
use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\CustomLogTable;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\ScopedLogParser;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\ScopedLogProvider;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\StubLogParser;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\StubLogProvider;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->plugin = filament('filament-log-viewer');

    actingAs($this->testUser);
});

afterEach(function () {
    $this->plugin->providerClass(LocalLogProvider::class);
    $this->plugin->parserClass(FileLogParser::class);
    $this->plugin->mailParserClass(DefaultMailParser::class);
    $this->plugin->stackTraceParserClass(DefaultStackTraceParser::class);
    $this->plugin->pageClass(LogTable::class);
});

it('Route A: swaps the data provider through the plugin', function () {
    $this->plugin->providerClass(StubLogProvider::class);

    expect($this->plugin->getProviderClass())->toBe(StubLogProvider::class);
    expect(app($this->plugin->getProviderClass()))->toBeInstanceOf(StubLogProvider::class);

    livewire(LogTable::class)
        ->assertCountTableRecords(1)
        ->assertSee('Stub provider injected row');
});

it('Route B: extends the default provider and overrides a protected method', function () {
    $this->initializeLogs();

    $this->plugin->providerClass(ScopedLogProvider::class);

    expect(app($this->plugin->getProviderClass()))->toBeInstanceOf(ScopedLogProvider::class);

    // laravel.log holds a single sample row; the override restricts discovery to it.
    livewire(LogTable::class)
        ->assertCountTableRecords(1)
        ->assertSee('Sample log');
});

it('Route B: extends the default page class through the plugin', function () {
    $this->plugin->pageClass(CustomLogTable::class);

    expect($this->plugin->getPageClass())->toBe(CustomLogTable::class);
    expect(CustomLogTable::customMarker())->toBe('custom-log-table-marker');

    livewire(CustomLogTable::class)
        ->assertSuccessful()
        ->assertSee('Log Table');
});

it('Route A: swaps the log parser through the plugin', function () {
    $this->initializeLogs();

    $this->plugin->parserClass(StubLogParser::class);

    expect($this->plugin->getParserClass())->toBe(StubLogParser::class);
    expect(app($this->plugin->getParserClass()))->toBeInstanceOf(StubLogParser::class);

    livewire(LogTable::class)
        ->assertSuccessful()
        ->assertSee('Stub parser injected row');
});

it('Route B: extends the default parser and overrides a public method', function () {
    $this->initializeLogs();

    $this->plugin->parserClass(ScopedLogParser::class);

    expect(app($this->plugin->getParserClass()))->toBeInstanceOf(ScopedLogParser::class);

    livewire(LogTable::class)
        ->assertSuccessful()
        ->assertSee('Sample log [scoped]');
});
