<?php

use FlixtechsLabs\LaravelAuthorizer\Commands\LaravelAuthorizerCommand;
use function PHPUnit\Framework\assertFileExists;
use function PHPUnit\Framework\assertStringContainsString;

it('can run the command successfully', function () {
    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'User',
        '--model' => 'User',
    ])->assertSuccessful();
});

it('can create policy when called with specific policy name', function () {
    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'Post',
        '--model' => 'Post',
    ])->assertSuccessful();

    assertFileExists(base_path('app/Policies/PostPolicy.php'));
});

it(
    'can create and append "Policy" to end of file name even if it\'s already passed',
    function () {
        $this->artisan(LaravelAuthorizerCommand::class, [
            'name' => 'UserPolicy',
            '--model' => 'User',
        ])->assertSuccessful();

        assertFileExists(base_path('app/Policies/UserPolicy.php'));
    }
);

it('can create policies for all models', function () {
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
        fn($model) => $this->artisan('make:model', [
            'name' => $model,
        ])
    );

    assertFileExists(app_path('Models/User.php'));
    assertFileExists(app_path('Models/Post.php'));
    //
    $this->artisan(LaravelAuthorizerCommand::class)->assertSuccessful();

    assertFileExists(base_path('app/Policies/UserPolicy.php'));
    assertFileExists(base_path('app/Policies/PostPolicy.php'));
    assertFileExists(base_path('app/Policies/CommentPolicy.php'));
    assertFileExists(base_path('app/Policies/TagPolicy.php'));
    assertFileExists(base_path('app/Policies/ProductPolicy.php'));
    assertFileExists(base_path('app/Policies/CategoryPolicy.php'));
    assertFileExists(base_path('app/Policies/OrderPolicy.php'));
    assertFileExists(base_path('app/Policies/OrderItemPolicy.php'));
});

it('can skip existing policies', function () {
    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'User',
        '--model' => 'User',
    ])->assertSuccessful();

    assertFileExists(base_path('app/Policies/UserPolicy.php'));

    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'User',
        '--model' => 'User',
    ])->assertSuccessful();
});

it('can force create policy even if it exists', function () {
    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'User',
        '--model' => 'User',
    ])->assertSuccessful();

    assertStringContainsString(
        'create user',
        file_get_contents(base_path('app/Policies/UserPolicy.php'))
    );

    $this->artisan(LaravelAuthorizerCommand::class, [
        'name' => 'User',
        '--model' => 'Post',
        '--force' => true,
    ])->assertSuccessful();

    assertFileExists(app_path('Policies/UserPolicy.php'));

    assertStringContainsString(
        'create post',
        file_get_contents(app_path('Policies/UserPolicy.php'))
    );
});
