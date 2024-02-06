<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;

trait LocatesModels
{
    /**
     * Get all models.
     */
    public function getModels(): Collection
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
                    ! $reflection->isAbstract();
            })
            ->map(fn ($model) => Str::afterLast($model, '\\'));
    }
}
