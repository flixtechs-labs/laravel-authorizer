<?php

namespace FlixtechsLabs\LaravelAuthorizer;

use Illuminate\Foundation\Console\AboutCommand;
use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAuthorizerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-authorizer')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-authorizer_table')
            ->hasCommand(LaravelAuthorizerCommand::class);
    }

    public function boot()
    {
        parent::boot();

        AboutCommand::add(
            'Laravel Authorizer',
            fn() => [
                'version' => '0.0.1',
                'author' => 'Flixtechs Labs',
                'license' => 'MIT',
            ]
        );
    }
}
