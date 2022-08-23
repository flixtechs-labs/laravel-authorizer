<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class GeneratePermissionsCommand extends Command
{
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
     */
    protected function generatePermissionsForAllModels(): void
    {
        $models = $this->getModels();

        $models->each(fn(string $model) => $this->generatePermissions($model));
    }

    /**
     * Generate permission for a given model.
     *
     * @param string $model
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

    public function generatePermission(string $model, string $permission)
    {
        if (
            Str::contains($permission, 'any') ||
            Str::contains($permission, 'all')
        ) {
            return Permission::updateOrCreate([
                'name' => $permission . ' ' . Str::plural(Str::lower($model)),
            ]);
        }

        return Permission::updateOrCreate([
            'name' => $permission . ' ' . Str::lower($model),
        ]);
    }

    /**
     * Get all models.
     *
     * @return array
     */
    public function getModels(): array|Collection
    {
        return collect(File::allFiles(app_path()))
            ->map(function (SplFileInfo $info) {
                $path = $info->getRelativePathname();

                return sprintf(
                    '\%s%s',
                    app()->getNamespace(),
                    Str::replace('/', '\\', Str::beforeLast($path, '.'))
                );
            })
            ->filter(function (string $class) {
                try {
                    $reflection = new ReflectionClass($class);
                } catch (ReflectionException $throwable) {
                    return false;
                }

                return $reflection->isSubclassOf(Model::class) &&
                    !$reflection->isAbstract();
            })
            ->map(fn($model) => Str::afterLast($model, '\\'));
    }
}