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
        $this->info('Creating .env file if it does not exist.');
        if (!file_exists('.env')) {
            copy('.env.example', '.env');
        }
        file_exists('.env')
            ? $this->info('✅ Success')
            : $this->error('❌ Error');

        if (env('DB_CONNECTION') == 'sqlite') {
            $sqlite_file = env('DB_DATABASE', 'database.sqlite');
            $sqlite_file_path = makeSqliteFile();

            $this->info("Emptying $sqlite_file file.");

            fopen($sqlite_file_path, 'w');

            file_exists($sqlite_file_path)
                ? $this->info('✅ Success')
                : $this->error('❌ Error');
        }

        $this->info('Generating application key.');
        $this->call('key:generate');

        return 0;
    }
}
