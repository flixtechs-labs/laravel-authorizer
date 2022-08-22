<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use Illuminate\Console\Command;

class LaravelAuthorizerCommand extends Command
{
    public $signature = 'laravel-authorizer';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
