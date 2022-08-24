<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use FlixtechsLabs\LaravelAuthorizer\Commands\Traits\LocatesModels;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Filesystem\Filesystem;

class LaravelAuthorizerCommand extends Command
{
    use LocatesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'authorizer:policies:generate {name? : The name of the policy to generate} {--m|model= : The model to use for the policy} {--f|force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Generate authorizations for your application';

    /**
     * Run the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->comment('Generating policies...');

        $name = $this->argument('name');
        $model = $this->option('model');

        if (is_null($name) && is_null($model)) {
            $this->generateAllPolicies();

            return self::SUCCESS;
        }

        if (is_null($name)) {
            $name = $model;
        }

        if (is_null($model)) {
            $this->generatePlainPolicy($name);

            return self::SUCCESS;
        }

        $this->generatePolicy($name, $model);

        return self::SUCCESS;
    }

    /**
     * Generate policies for all models.
     *
     * @return void
     */
    public function generateAllPolicies(): void
    {
        $this->getModels()->each(
            fn(string $model) => $this->generatePolicy($model, $model)
        );
    }

    /**
     * Generate a plain policy without a model.
     *
     * @param  string  $name
     * @return void
     */
    public function generatePlainPolicy(string $name): void
    {
        if (
            file_exists($this->getPolicyPath($name)) &&
            !$this->option('force')
        ) {
            $this->error(sprintf('Policy "%s" already exists!', $name));

            return;
        }

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

        (new Filesystem())->dumpFile($this->getPolicyPath($name), $compiled);
    }

    /**
     * Generate a policy for a given model.
     *
     * @param  string  $name
     * @param  string  $model
     * @return void
     */
    private function generatePolicy(string $name, string $model): void
    {
        if (
            file_exists($this->getPolicyPath($name)) &&
            !$this->option('force')
        ) {
            $this->error(sprintf('Policy "%s" already exists!', $name));

            return;
        }

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

        (new Filesystem())->dumpFile($this->getPolicyPath($name), $compiled);
    }

    /**
     * Get the path to the policy.
     *
     * @param  string  $name
     * @return string
     */
    public function getPolicyPath(string $name): string
    {
        return app_path('Policies/' . $this->getClassName($name) . '.php');
    }

    /**
     * Get the policies' namespace.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return app()->getNamespace() . 'Policies';
    }

    /**
     * Get the class name for the policy.
     *
     * @param  string  $name The name of the policy
     * @return string
     */
    public function getClassName(string $name): string
    {
        if (Str::endsWith(Str::lower($name), 'policy')) {
            return Str::studly($name);
        }

        return Str::studly($name) . 'Policy';
    }

    /**
     * Get the namespace for the model.
     *
     * @param  string  $model The name of the model
     * @return string
     */
    public function getNamespacedModel(string $model): string
    {
        return app()->getNamespace() . 'Models\\' . Str::studly($model);
    }

    /**
     * Get the namespace for the User model.
     *
     * @return string
     */
    public function getNamespacedUserModel(): string
    {
        return config('auth.providers.users.model');
    }

    /**
     * Get the path to the stub.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__ . '/stubs/policy.stub';
    }
}
