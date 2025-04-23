<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;

class LaravelAuthorizerTest extends TestCase
{
    public function test_can_run_tests(): void
    {
        $this->assertTrue(true);
    }

    public function test_can_run_command_successfully(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'User',
            '--model' => 'User',
        ])->assertSuccessful();
    }

    public function test_can_create_policy_when_called_with_specific_policy_name(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'Post',
            '--model' => 'Post',
        ])->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/PostPolicy.php'));
    }

    public function test_can_append_policy_to_filename_event_if_it_was_included_in_policy_name(): void
    {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'UserPolicy',
            '--model' => 'User',
        ])->assertSuccessful();

        $this->assertFileExists(base_path('app/Policies/UserPolicy.php'));
    }

    public function test_can_generate_policies_for_all_models(): void
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

    public function test_can_skip_existing_policies(): void
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

    public function test_can_force_create_policy_even_if_it_exists(): void
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
