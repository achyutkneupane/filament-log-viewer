<?php

declare(strict_types=1);

namespace AchyutN\FilamentLogViewer\Tests;

use AchyutN\FilamentLogViewer\LogViewerProvider;
use AchyutN\FilamentLogViewer\Tests\Providers\TestPanelProvider;
use AllowDynamicProperties;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Log\LogServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

#[AllowDynamicProperties]
abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            ActionsServiceProvider::class,
            LogServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            LogViewerProvider::class,
            SchemasServiceProvider::class,
            TestPanelProvider::class,
        ];

        sort($providers);

        return $providers;
    }

    protected function defineEnvironment($app): void
    {
        config()->set('database.connections.the_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('database.default', 'the_test');
        config()->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
    }

    protected function writeLog(string $filename, string $content): void
    {
        file_put_contents(storage_path("logs/{$filename}"), $content);
    }

    protected function initializeLogs(): void
    {
        if (! is_dir(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0755, true);
        }

        $this->writeLog('laravel.log', '[2024-08-06 20:15:00] local.ERROR: Sample log');
        $this->writeLog('other.log', '[2024-08-06 20:16:00] local.INFO: Another log');
        $this->writeLog('not-a-log.txt', 'This is not a log');

        $stackTraces = [
            '[2024-08-06 20:17:00] local.ERROR: Sample log with stack trace at /path/to/file.php:123',
            '[stacktrace]',
            '#0 /path/to/another_file.php(456): someFunction()',
            '#1 {main}',
            '"}',
            '[2024-08-06 20:18:00] local.ERROR: Another log with stack trace at /path/to/another_file.php:789',
            '[stacktrace]',
            '#0 /path/to/yet_another_file.php(101): anotherFunction()',
            '#1 {main}',
            '"}',
        ];
        $this->writeLog('stack-trace.log', implode("\n", $stackTraces));
    }

    public function writeMailLog(string $filename = "laravel.log"): void
    {
        $mailContentArray = [
            '[2025-08-09 00:18:13] local.DEBUG: From: Laravel <hello@example.com>',
            'To: Achyut Neupane <achyutkneupane@gmail.com>',
            'Subject: Test Email',
            'MIME-Version: 1.0',
            'Date: Sat, 09 Aug 2025 00:18:13 +0545',
            'Message-ID: <1826c88fdd369252a413b8fa7f106c26@example.com>',
            'Content-Type: multipart/alternative; boundary=OcFZ3Fi3',
            '',
            '--OcFZ3Fi3',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            'This is a test email sent from Laravel.',
            '',
            '--OcFZ3Fi3',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            '<p>This is a test email sent from Laravel.</p>',
            '',
            '--OcFZ3Fi3--',
        ];

        if (! is_dir(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0755, true);
        }

        file_put_contents(storage_path("logs/{$filename}"), implode("\n", $mailContentArray));
    }

    protected function deleteAllLogs(): void
    {
        collect(glob(storage_path('logs/*')))->each(function ($file) {
            if (is_file($file)) {
                unlink($file);
            }
        });
    }

    protected function setUpDatabase(): void
    {
        app('db')->connection()->getSchemaBuilder()->table('users', function (Blueprint $blueprint) {
            $blueprint->enum('role', ['admin', 'user'])->default('user');
        });

        $this->testUser = Model\User::query()
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@achyut.com.np',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
    }
}
