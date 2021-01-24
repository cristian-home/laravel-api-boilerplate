<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar proyecto';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creating .env file.');
        if (!file_exists('.env')) {
            copy('.env.example', '.env');
        }
        file_exists('.env')
            ? $this->info('✅ Success')
            : $this->error('❌ Error');

        $this->info('Creating sqlite database file.');
        fopen('database/database.sqlite', 'w');
        file_exists('database/database.sqlite')
            ? $this->info('✅ Success')
            : $this->error('❌ Error');

        $this->info('Generating application key.');
        $this->call('key:generate');

        return 0;
    }
}
