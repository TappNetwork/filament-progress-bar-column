<?php

namespace Tapp\FilamentProgressBarColumn\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Tapp\FilamentProgressBarColumn\FilamentProgressBarColumnServiceProvider;

class TestCase extends Orchestra
{
    /**
     * The latest response from the application.
     *
     * @var \Illuminate\Testing\TestResponse|null
     */
    protected static $latestResponse;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Tapp\\FilamentProgressBarColumn\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            FilamentProgressBarColumnServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $migration = include __DIR__ . '/database/migrations/create_test_table.php';
        $migration->up();
    }
}
