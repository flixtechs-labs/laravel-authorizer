<?php

use FlixtechsLabs\LaravelAuthorizer\Commands\SetupCommand;

it('can setup the authorizer package', function () {
    $this->artisan(SetupCommand::class)
        ->expectsOutput('Setup complete!')
        ->assertSuccessful();
});

it('can generate permissions on setup', function () {
    $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

    $this->artisan(SetupCommand::class, [
        '--permissions' => true,
    ])->assertSuccessful();

    $this->assertDatabaseHas('permissions', [
        'name' => 'create user',
    ]);
});

it('can generate policies on setup', function () {
    $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

    $this->artisan(SetupCommand::class, [
        '--policies' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Policies/UserPolicy.php'));
});
