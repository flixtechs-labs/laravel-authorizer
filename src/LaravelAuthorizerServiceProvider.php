<?php

namespace FlixtechsLabs\LaravelAuthorizer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;

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
}
