<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests\Feature;

use AchyutN\FilamentLogViewer\Contracts\LogProvider;
use AchyutN\FilamentLogViewer\Enums\LogLevel;
use AchyutN\FilamentLogViewer\LogTable;
use AchyutN\FilamentLogViewer\Model\Log;
use AchyutN\FilamentLogViewer\Providers\LocalLogProvider;
use AchyutN\FilamentLogViewer\Tests\Feature\Stubs\ReadOnlyLogProvider;
use Carbon\Carbon;
use Config;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use function Pest\Livewire\livewire;

/**
 * @return array<int, int|string>
 */
function firstIndexWhere(callable $predicate): ?int
{
    /** @var LogProvider $provider */
    $provider = app(LogProvider::class);

    foreach ($provider->getRows(true) as $index => $row) {
        if ($predicate($row)) {
            return $index;
        }
    }

    return null;
}

beforeEach(function () {
    $this->initializeLogs();
});

afterEach(function () {
    $this->deleteAllLogs();
});

it('renders successfully', function () {
    livewire(LogTable::class)
        ->assertSuccessful()
        ->assertSee('Log Table');
});

describe('actions', function () {
    it('has actions', function () {
        livewire(LogTable::class)
            ->assertActionExists('refresh', function (Action $action) {
                return $action->getLabel() === 'Refresh' &&
                    $action->isIconButton() &&
                    $action->getTooltip() === 'Refresh';
            })
            ->assertActionExists('clear', function (Action $action) {
                return $action->getLabel() === 'Clear Logs' &&
                    ! $action->isOutlined() &&
                    $action->getColor() === Color::Red;
            });
    });

    it('hides clear action when config set to false', function () {
        Config::set('filament-log-viewer.enable_delete', false);

        livewire(LogTable::class)
            ->assertActionExists('clear', fn (Action $action) => ! $action->isVisible());
    });

    it("refreshes logs on 'refresh' action", function () {
        livewire(LogTable::class)
            ->assertCountTableRecords(4)
            ->callAction('refresh')
            ->assertSuccessful()
            ->assertCountTableRecords(4);
    });

    it("clears logs on 'clear' action", function () {
        livewire(LogTable::class)
            ->assertCountTableRecords(4)
            ->callAction('clear')
            ->assertSuccessful()
            ->assertCountTableRecords(0);
    });

    it("show notification on 'clear' action", function () {
        livewire(LogTable::class)
            ->callAction('clear')
            ->assertSuccessful()
            ->mountAction('submit')
            ->assertNotified('All logs have been cleared!');
    });
});

describe('clear individual file', function () {
    it('shows a per-file dropdown when multiple log files exist', function () {
        livewire(LogTable::class)
            ->assertActionExists('clear-file-laravel-log', fn (Action $action) => $action->isVisible())
            ->assertActionExists('clear-file-other-log', fn (Action $action) => $action->isVisible())
            ->assertActionExists('clear-file-stack-trace-log', fn (Action $action) => $action->isVisible());
    });

    it('clears only the selected file from the dropdown', function () {
        livewire(LogTable::class)
            ->assertCountTableRecords(4)
            ->callAction('clear-file-other-log')
            ->assertSuccessful()
            ->assertCountTableRecords(3);

        expect(file_get_contents(storage_path('logs/other.log')))->toBe('');
        expect(file_get_contents(storage_path('logs/laravel.log')))->not->toBe('');
    });

    it('shows a plain clear action when only one log file exists', function () {
        foreach (Log::getAllLogFiles() as $existingFile) {
            @unlink(storage_path('logs/'.$existingFile));
        }
        $this->writeLog('only.log', '[2024-08-06 20:15:00] local.ERROR: Only log');

        livewire(LogTable::class)
            ->assertActionExists('clear', fn (Action $action) => $action->isVisible())
            ->assertActionDoesNotExist('clear-file-only-log');
    });

    it('hides the per-file actions when delete is disabled', function () {
        Config::set('filament-log-viewer.enable_delete', false);

        livewire(LogTable::class)
            ->assertActionExists('clear-file-laravel-log', fn (Action $action) => ! $action->isVisible())
            ->assertActionExists('clear-file-other-log', fn (Action $action) => ! $action->isVisible());
    });

    it('hides the clear actions for read-only providers', function () {
        filament('filament-log-viewer')->providerClass(ReadOnlyLogProvider::class);

        livewire(LogTable::class)
            ->assertSuccessful()
            ->assertActionDoesNotExist('clear')
            ->assertActionDoesNotExist('clear-file');

        filament('filament-log-viewer')->providerClass(LocalLogProvider::class);
    });
});

describe('columns', function () {
    it('has table columns', function () {
        livewire(LogTable::class)
            ->assertTableColumnExists('date')
            ->assertTableColumnExists('env')
            ->assertTableColumnExists('log_level')
            ->assertTableColumnExists('message')
            ->assertTableColumnExists('file');
    });

    it('has badge in log_level column', function () {
        livewire(LogTable::class)
            ->assertCanRenderTableColumn('log_level')
            ->assertTableColumnExists('log_level', function (TextColumn $column) {
                return $column->isBadge();
            });
    });

    it('has Badge & Color in env column', function () {
        livewire(LogTable::class)
            ->assertCanNotRenderTableColumn('env')
            ->assertTableColumnExists('env', function (TextColumn $column) {
                return $column->isBadge() &&
                    $column->getColor('local') === Color::Blue &&
                    $column->getColor('production') === Color::Red &&
                    $column->getColor('staging') === Color::Orange &&
                    $column->getColor('testing') === Color::Gray &&
                    $column->getColor('default') === Color::Yellow;
            });
    });

    it('has table row\'s actions', function () {
        livewire(LogTable::class)
            ->assertTableColumnExists('date', function (TextColumn $column) {
                $viewAction = array_key_exists('view', $column->getTable()->getFlatActions())
                    ? $column->getTable()->getFlatActions()['view']
                    : null;

                expect($viewAction)
                    ->toBeInstanceOf(Action::class);

                $labelCheck = $viewAction->getLabel() === 'View';
                $nameCheck = $viewAction->getName() === 'view';
                $colorCheck = $viewAction->getColor() === Color::Gray;
                $iconCheck = $viewAction->getIcon() === Heroicon::Eye;

                return $labelCheck && $nameCheck && $colorCheck && $iconCheck;
            });
    });
});

describe('filters', function () {
    it('has table filters', function () {
        livewire(LogTable::class)
            ->assertTableFilterExists('date')
            ->assertTableFilterExists('file');
    });

    it('has date filter', function () {
        livewire(LogTable::class)
            ->assertTableFilterExists('date', function (Filter $filter) {
                return $filter->getName() === 'date' &&
                    $filter->getLabel() === 'Date';
            });
    });

    it('has file selector filter', function () {
        livewire(LogTable::class)
            ->assertTableFilterExists('file', function (SelectFilter $filter) {
                expect($filter)
                    ->toBeInstanceOf(SelectFilter::class);

                return $filter->getName() === 'file' &&
                    $filter->getLabel() === 'File' &&
                    $filter->getOptions() === Log::getFilesForFilter() &&
                    $filter->getIndicator() === 'File';
            });
    });

    it('has indicators for date range', function () {
        livewire(LogTable::class)
            ->filterTable('date', [
                'from' => Carbon::create(2023)->toDateString(),
                'until' => Carbon::create(2023, 12, 31)->toDateString(),
            ])
            ->assertSeeText('Logs from Jan 1, 2023 to Dec 31, 2023');

        livewire(LogTable::class)
            ->filterTable('date', [
                'from' => Carbon::create(2023)->toDateString(),
                'until' => null,
            ])
            ->assertSeeText('Logs from Jan 1, 2023');

        livewire(LogTable::class)
            ->filterTable('date', [
                'from' => null,
                'until' => Carbon::create(2023, 12, 31)->toDateString(),
            ])
            ->assertSeeText('Logs until Dec 31, 2023');

        livewire(LogTable::class)
            ->filterTable('date', [
                'from' => null,
                'until' => null,
            ])
            ->assertDontSeeText('Logs from')
            ->assertDontSeeText('Logs until');
    });

    it('has indicators for file filter', function () {
        livewire(LogTable::class)
            ->filterTable('file', 'other.log')
            ->assertSeeText('File: other.log');

        livewire(LogTable::class)
            ->filterTable('file', null)
            ->assertDontSeeText('File:');
    });

    it('table is unscoped by default', function () {
        $unscopedLogLevel = livewire(LogTable::class)
            ->get('unscopedLogLevel');

        expect(livewire(LogTable::class)->get('unscopedLogLevel'))
            ->toBe($unscopedLogLevel);

        expect(livewire(LogTable::class)->get('activeTab'))
            ->toBeIn([$unscopedLogLevel, null]);
    });
});

describe('record actions (slide-over regression)', function () {
    it('renders the stack-trace slide-over without throwing', function () {
        $index = firstIndexWhere(fn (array $row): bool => $row['has_stack']);

        expect($index)->not()->toBeNull();

        livewire(LogTable::class)
            ->mountTableAction('view', (string) $index)
            ->assertSuccessful();
    });

    it('renders the json slide-over without throwing', function () {
        $this->writeLog('context.log', '[2024-08-06 20:19:00] local.INFO: User logged in {"user_id": 5, "ip": "127.0.0.1"}');

        $index = firstIndexWhere(fn (array $row): bool => $row['context'] !== null);

        expect($index)->not()->toBeNull();

        livewire(LogTable::class)
            ->mountTableAction('view-json', (string) $index)
            ->assertSuccessful();
    });

    it('renders the mail slide-over without throwing', function () {
        $this->writeMailLog();

        $index = firstIndexWhere(fn (array $row): bool => $row['log_level'] === LogLevel::MAIL);

        expect($index)->not()->toBeNull();

        livewire(LogTable::class)
            ->mountTableAction('read', (string) $index)
            ->assertSuccessful();
    });

    it('calls the slide-over schema only after a full re-parse budget', function () {
        /** @var LogProvider $provider */
        $provider = app(LogProvider::class);
        $provider->getRows(true);
        $rows = $provider->getRows();
        expect($rows)->not()->toBeEmpty();

        livewire(LogTable::class)
            ->mountTableAction('view', '0')
            ->assertSuccessful();
    });
});
