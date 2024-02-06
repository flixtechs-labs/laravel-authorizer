<?php

namespace FlixtechsLabs\LaravelAuthorizer;

use FlixtechsLabs\LaravelAuthorizer\Commands\Traits\LocatesModels;
use Illuminate\Support\Str;

class LaravelAuthorizer
{
    use LocatesModels;

    public function getPermissionsFor(string $model): array
    {
        return array_map(static function (string $permission) use ($model) {
            return $permission.' '.(Str::contains($permission, 'all') ? Str::of($model)->snake()->plural()->lower() : Str::of($model)->snake()->lower());
        }, config('authorizer.permissions'));
    }
}
