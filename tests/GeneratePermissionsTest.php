<?php

namespace FlixtechsLabs\LaravelAuthorizer\Tests;

use FlixtechsLabs\LaravelAuthorizer\Commands\GeneratePermissionsCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function testCanAlsoRunTests(): void
    {
        $this->assertTrue(true);
    }

    public function testCanGeneratePermissionsForAllModels(): void
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

        $this->artisan(GeneratePermissionsCommand::class)
            ->expectsOutput('Generating permissions...')
            ->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'create post',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'update post',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'delete post',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'create comment',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'update comment',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'delete comment',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'create tag',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'update tag',
        ]);
    }

    public function testCanGeneratePermissionForJustTheSpecifiedModel(): void
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

        $this->artisan(GeneratePermissionsCommand::class, [
            '--model' => 'Post',
        ])
            ->expectsOutput('Generating permissions...')
            ->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'create post',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'update post',
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'delete tag',
        ]);
    }
}
