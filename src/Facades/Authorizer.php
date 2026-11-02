<?php

namespace FlixtechsLabs\LaravelAuthorizer\Facades;

use FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizer;
use Illuminate\Support\Facades\Facade;

/**
 * @see LaravelAuthorizer
 */
class Authorizer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LaravelAuthorizer::class;
    }
}
