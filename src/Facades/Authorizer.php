<?php

namespace FlixtechsLabs\LaravelAuthorizer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizer
 */
class Authorizer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \FlixtechsLabs\LaravelAuthorizer\LaravelAuthorizer::class;
    }
}
