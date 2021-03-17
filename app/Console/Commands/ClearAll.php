<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar caches';

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
        $this->info('Clear all caches.');

        $this->call('view:clear');
        $this->call('logs:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('sessions:clear');
        $this->call('clear-compiled');
        $this->call('cache-app:clear');
        $this->call('test-cache:clear');
        $this->call('telescope-app:clear');

        return 0;
    }
}
