<?php

namespace FlixtechsLabs\LaravelAuthorizer\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\PermissionServiceProvider;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizer:setup {--p|permissions : Generate all permissions} {--P|policies : Generate all policies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the authorizer package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--provider' => PermissionServiceProvider::class,
        ]);

        $this->call('migrate');

        if ($this->option('permissions')) {
            $this->info('Generating permissions...');
            $this->call('authorizer:permissions:generate');
        }

        if ($this->option('policies')) {
            $this->info('Generating policies...');
            $this->call('authorizer:policies:generate');
        }

        $this->info('Setup complete!');

        return self::SUCCESS;
    }
}
