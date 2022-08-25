<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;

class LaravelAuthorizerTest extends TestCase
{
    public function testCanRunTests(): void
    {
        $this->assertTrue(true);
    }

    public function testCanRunCommandSuccessfully(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'User',
        ])->assertSuccessful();
    }

    public function testCanCreatePolicyWhenCalledWithSpecificPolicyName(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'Post',
            '--model' => 'Post',
        ])->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/PostPolicy.php'));
    }

    public function testCanAppendPolicyToFilenameEventIfItWasIncludedInPolicyName(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'UserPolicy',
            '--model' => 'User',
        ])->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/UserPolicy.php'));
    }

    public function testCanGeneratePoliciesForAllModels(): void
    {
        collect([
            'User',
            'Post',
            'Comment',
            'Tag',
            'Product',
            'Category',
            'Order',
            'OrderItem',
        ])->each(
            fn ($model) => $this->artisan('make:model', [
                'name' => $model,
            ])
        );

        $this->assertFileExists(app_path('Models/User.php'));
        $this->assertFileExists(app_path('Models/Post.php'));

        $this->artisan(LaravelAuthorizerCommand::class)->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/UserPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/PostPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/CommentPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/TagPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/ProductPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/CategoryPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/OrderPolicy.php'));
        $this->assertFileExists(base_path('app/Policies/OrderItemPolicy.php'));
    }

    public function testCanSkipExistingPolicies(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'User',
        ])->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/UserPolicy.php'));

        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'User',
        ])->assertSuccessful();
    }

    public function testCanForceCreatePolicyEvenIfItExists(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'User',
        ])->assertSuccessful();

        $this->assertStringContainsString(
            'create user',
            file_get_contents(base_path('app/Policies/UserPolicy.php'))
        );

        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'Post',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertFileExists(app_path('Policies/UserPolicy.php'));

        $this->assertStringContainsString(
            'create post',
            file_get_contents(app_path('Policies/UserPolicy.php'))
        );
    }
}
