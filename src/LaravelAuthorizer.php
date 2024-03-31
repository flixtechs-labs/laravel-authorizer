<?php

namespace FlixtechsLabs\LaravelAuthorizer;

use FlixtechsLabs\LaravelAuthorizer\Commands\Traits\LocatesModels;
use Illuminate\Support\Str;

class LaravelAuthorizer
{
    use LocatesModels;

    public function getPermissionsFor(string $model): array
    {
        $chain = Str::of($model)->afterLast('\\');

        return array_map(static function (string $permission) use ($chain) {
            return $permission.' '.(Str::contains($permission, 'all') ? $chain->snake()->plural()->lower() : $chain->snake()->lower());
        }, config('authorizer.permissions'));
    }
}
