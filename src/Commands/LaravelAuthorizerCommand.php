<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class LaravelAuthorizerCommand extends Command
{
    public $signature = 'authorizer:generate {name? : The name of the policy to generate} {--m|model= : The model to use for the policy}';

    public $description = 'Generate authorizations for your application';

    public function handle(): int
    {
        $this->comment('Generating policies...');

        $name = $this->argument('name');
        $model = $this->option('model');

        if (is_null($name) && is_null($model)) {
            $this->generateAllPolicies();
        }
        //
        //        if (is_null($model)) {
        //            $this->generatePlainPolicy($name);
        //        }
        //
        //        if (is_null($name)) {
        //            $name = $model;
        //        }
        //
        //        $this->generatePolicy($name, $model);

        return self::SUCCESS;
    }

    public function generateAllPolicies(): void
    {
        $models = collect(File::allFiles(app_path()))
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

        $models->each(function (string $model) {
            if (file_exists($this->getPolicyPath($model))) {
                return;
            }

            $this->generatePolicy($model, $model);
        });
    }

    public function generatePlainPolicy(string $name): void
    {
        $compiled = collect([
            'namespacedUserModel' => $this->getNamespacedUserModel(),
            'namespace' => $this->getNamespace(),
            'class' => $this->getClassName($name),
        ])->reduce(
            fn($carry, $value, $key) => Str::replace(
                '{{ ' . $key . ' }}',
                $value,
                $carry
            )
        );

        (new Filesystem())->dumpFile($this->getPath($name), $compiled);
    }

    public function getPolicyPath(string $name): string
    {
        return $this->getPath($name);
    }

    private function generatePolicy(string $name, string $model): void
    {
        $compiled = collect([
            'name' => $name,
            'model' => $model,
            'modelVariable' =>
                strtolower($model) ===
                strtolower(
                    Str::afterLast($this->getNamespacedUserModel(), '\\')
                )
                    ? 'model'
                    : strtolower($model),
            'modelSingularLowerCase' => strtolower(Str::singular($model)),
            'modelPluralLowerCase' => strtolower(Str::plural($model)),
            'namespace' => $this->getNamespace(),
            'class' => $this->getClassName($name),
            'namespacedModel' => $this->getNamespacedModel($model),
            'namespacedUserModel' => $this->getNamespacedUserModel(),
            'user' => Str::afterLast($this->getNamespacedUserModel(), '\\'),
        ])->reduce(
            static fn($old, $value, $key) => Str::replace(
                '{{ ' . $key . ' }}',
                $value,
                $old
            ),
            file_get_contents($this->getStub())
        );

        (new Filesystem())->dumpFile($this->getPath($name), $compiled);
    }

    public function getNamespace(): string
    {
        return app()->getNamespace() . 'Policies';
    }

    public function getClassName(string $name): string
    {
        if (Str::endsWith($name, 'policy')) {
            return Str::studly($name);
        }

        return Str::studly($name) . 'Policy';
    }

    public function getNamespacedModel(string $model): string
    {
        return app()->getNamespace() . 'Models\\' . Str::studly($model);
    }

    public function getNamespacedUserModel(): string
    {
        return config('auth.providers.users.model');
    }

    public function getPath(string $name): string
    {
        return app_path('Policies/' . $this->getClassName($name) . '.php');
    }

    public function getStub(): string
    {
        return __DIR__ . '/stubs/policy.stub';
    }
}
