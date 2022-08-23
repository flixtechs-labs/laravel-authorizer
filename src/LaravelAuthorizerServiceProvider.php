<?php

namespace FlixtechsLabs\LaravelAuthorizer;

use FlixtechsLabs\LaravelAuthorizer\Commands\GeneratePermissionsCommand;
use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;
use Illuminate\Foundation\Console\AboutCommand;
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
            ->hasCommands([
                LaravelAuthorizerCommand::class,
                GeneratePermissionsCommand::class,
            ]);
    }

    public function boot()
    {
        parent::boot();

        AboutCommand::add(
            'Laravel Authorizer',
            fn () => [
                'version' => '0.0.1',
                'author' => 'Flixtechs Labs',
                'license' => 'MIT',
            ]
        );
    }
}
