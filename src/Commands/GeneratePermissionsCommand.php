<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use FlixtechsLabs\LaravelAuthorizer\Commands\Traits\LocatesModels;
use FlixtechsLabs\LaravelAuthorizer\Facades\Authorizer;
use Illuminate\Console\Command;
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
            fn (string $model) => $this->generatePermissions($model)
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
        $permissions = Authorizer::getPermissionsFor($model);

        collect($permissions)->each(
            fn (string $permission) => Permission::findOrCreate(
                $permission
            )
        );
    }
}
