<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\GeneratePermissionsCommand;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can generate permissions', function () {
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

    $this->artisan(GeneratePermissionsCommand::class)
        ->expectsOutput('Generating permissions...')
        ->assertSuccessful();

    assertDatabaseHas('permissions', [
        'name' => 'create post',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'update post',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'delete post',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'create comment',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'update comment',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'delete comment',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'create tag',
    ]);
    assertDatabaseHas('permissions', [
        'name' => 'update tag',
    ]);
});

it('can generate permission for just one model', function () {
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

    $this->artisan(GeneratePermissionsCommand::class, [
        '--model' => 'Post',
    ])
        ->expectsOutput('Generating permissions...')
        ->assertSuccessful();

    assertDatabaseHas('permissions', [
        'name' => 'create post',
    ]);

    assertDatabaseHas('permissions', [
        'name' => 'update post',
    ]);

    assertDatabaseMissing('permissions', [
        'name' => 'delete tag',
    ]);
});
