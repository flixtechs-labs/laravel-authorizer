<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\SetupCommand;

class SetupTest extends TestCase
{
    public function test_can_run_test(): void
    {
        $this->assertTrue(true);
    }

    public function test_can_setup_the_authorizer_package(): void
    {
        $this->artisan(SetupCommand::class)
            ->expectsOutput('Setup complete!')
            ->assertSuccessful();
    }

    public function test_can_generate_permissions_during_setup(): void
    {
        $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

        $this->artisan(SetupCommand::class, [
            '--permissions' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'create user',
        ]);
    }

    public function test_can_generate_policies_during_setup(): void
    {
        $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

        $this->artisan(SetupCommand::class, [
            '--policies' => true,
        ])->assertSuccessful();

        $this->assertFileExists(app_path('Policies/UserPolicy.php'));
    }
}
