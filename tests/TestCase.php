<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->beforeApplicationDestroyed(function () {
            File::cleanDirectory(app_path('Models'));
            File::cleanDirectory(app_path('Policies'));
            File::deleteDirectory(app_path('Policies'));
            File::cleanDirectory(database_path('migrations'));
            File::delete(config_path('permission.php'));
        });

        Factory::guessFactoryNamesUsing(
            fn (
                string $modelName
            ) => 'FlixtechsLabs\\LaravelAuthorizer\\Database\\Factories\\'.
                class_basename($modelName).
                'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [LaravelAuthorizerServiceProvider::class];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => 'database/database.sqlite',
            'prefix' => '',
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-authorizer_table.php.stub';
        $migration->up();
        */
    }

    public function defineDatabaseMigrations()
    {
        $this->artisan('vendor:publish', [
            '--provider' => PermissionServiceProvider::class,
        ])->run();

        //        $this->loadLaravelMigrations();

        //$this->loadMigrationsFrom(database_path('migrations'));

        //        $this->artisan('migrate:fresh')->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback')->run();
        });
    }

    /**
     * Ignore package discovery from.
     *
     * @return array<int, array>
     */
    public function ignorePackageDiscoveriesFrom(): array
    {
        return [];
    }
}
