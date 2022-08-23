<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->beforeApplicationDestroyed(function () {
            File::cleanDirectory(app_path('Models'));
            File::cleanDirectory(app_path('Policies'));
        });

        Factory::guessFactoryNamesUsing(
            fn(
                string $modelName
            ) => 'FlixtechsLabs\\LaravelAuthorizer\\Database\\Factories\\' .
                class_basename($modelName) .
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

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-authorizer_table.php.stub';
        $migration->up();
        */
    }
}
