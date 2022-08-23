<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use FlixtechsLabs\LaravelAuthorizer\Commands\Traits\LocatesModels;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GeneratePermissionsCommand extends Command
{
    use LocatesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizer:permissions:generate {--m|model= : The model to use for the policy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions for your application';

    /**
     * Run the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->comment('Generating permissions...');

        $model = $this->option('model');

        if (is_null($model)) {
            $this->generatePermissionsForAllModels();

            return self::SUCCESS;
        }

        $this->generatePermissions($model);

        return self::SUCCESS;
    }

    /**
     * Generate all permissions.
     *
     * @return void
     */
    protected function generatePermissionsForAllModels(): void
    {
        $this->getModels()->each(
            fn(string $model) => $this->generatePermissions($model)
        );
    }

    /**
     * Generate permission for a given model.
     *
     * @param  string  $model
     * @return void
     */
    public function generatePermissions(string $model): void
    {
        $permissions = config('authorizer.permissions');

        collect($permissions)->each(
            fn(string $permission) => $this->generatePermission(
                $model,
                $permission
            )
        );
    }

    /**
     * Generate a permission for a given model.
     *
     * @param  string  $model
     * @param  string  $permission
     * @return mixed
     */
    public function generatePermission(string $model, string $permission): mixed
    {
        if (
            Str::contains($permission, 'any') ||
            Str::contains($permission, 'all')
        ) {
            return Permission::findOrCreate(
                $permission . ' ' . Str::snake(Str::plural(Str::lower($model)))
            );
        }

        return Permission::findOrCreate(
            $permission . ' ' . Str::snake(Str::lower($model))
        );
    }
}
