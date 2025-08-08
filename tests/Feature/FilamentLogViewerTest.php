<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->plugin = filament('filament-log-viewer');

    actingAs($this->testUser);
});

afterEach(function () {
    $this->deleteAllLogs();
});

it('renders with default settings', function () {
    $this->get($this->plugin->getNavigationUrl())
        ->assertSuccessful()
        ->assertSee('Log Table');
});

it('can customize navigation label', function () {
    $this->plugin->navigationLabel('Custom Log Viewer');

    $this->get($this->plugin->getNavigationUrl())
        ->assertSuccessful()
        ->assertSee('Custom Log Viewer');
});

it('only gives access to authorized users', function () {
    $this->plugin->authorize(fn () => auth()->user()->role === 'admin');

    $this->get($this->plugin->getNavigationUrl())
        ->assertForbidden();
});

it('allows customization of navigation group', function () {
    $this->plugin->navigationGroup('Custom Group');

    $this->get($this->plugin->getNavigationUrl())
        ->assertSuccessful()
        ->assertSee('Custom Group');
});

describe('tabs', function () {
    it('shows log viewer UI with tabs', function () {
        $this->get($this->plugin->getNavigationUrl())
            ->assertSuccessful()
            ->assertSee('All Logs')
            ->assertSee('fi-badge')
            ->assertSee('fi-active');
    });


    it('doesn\'t show mail tab if no mail log', function () {
        $this->get($this->plugin->getNavigationUrl())
            ->assertSuccessful()
            ->assertDontSee('Mail');
    });

    it('shows mail tab if mail log exists', function () {
        $this->writeMailLog();

        $this->get($this->plugin->getNavigationUrl())
            ->assertSuccessful()
            ->assertSee('Mail')
            ->assertSee('fi-badge');
    });
});
