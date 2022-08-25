<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends BaseTestCase
{
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
            fn(
                string $modelName
            ) => 'FlixtechsLabs\\LaravelAuthorizer\\Database\\Factories\\' .
                class_basename($modelName) .
                'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelAuthorizerServiceProvider::class];
    }

    public function defineDatabaseMigrations(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PermissionServiceProvider::class,
        ])->run();
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    /**
     * Ignore package discovery from.
     *
     * @return array<int, string>
     */
    public function ignorePackageDiscoveriesFrom(): array
    {
        return [];
    }
}
