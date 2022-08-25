<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\SetupCommand;

class SetupTest extends TestCase
{
    public function testCanRunTest(): void
    {
        $this->assertTrue(true);
    }

    public function testCanSetupTheAuthorizerPackage(): void
    {
        $this->artisan(SetupCommand::class)
            ->expectsOutput('Setup complete!')
            ->assertSuccessful();
    }

    public function testCanGeneratePermissionsDuringSetup(): void
    {
        $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

        $this->artisan(SetupCommand::class, [
            '--permissions' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'create user',
        ]);
    }

    public function testCanGeneratePoliciesDuringSetup(): void
    {
        $this->artisan('make:model', ['name' => 'User'])->assertSuccessful();

        $this->artisan(SetupCommand::class, [
            '--policies' => true,
        ])->assertSuccessful();

        $this->assertFileExists(app_path('Policies/UserPolicy.php'));
    }
}
