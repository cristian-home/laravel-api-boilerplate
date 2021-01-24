<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectSetUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar proyecto';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up project.');
        $this->call('clear:all');
        $this->call('migrate:fresh');
        $this->call('passport:install', ['--force' => 'default']);
        $this->info('Creating web app passport client.');
        $this->call('passport:client', [
            '--password' => 'default',
            '--name' => config('services.passport.oauth.clients.webapp.name'),
            '--provider' => 'users',
        ]);

        $this->call('db:seed');

        return 0;
    }
}
