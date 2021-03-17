<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache-app:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear app cache.';

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
        $cache_driver = env('CACHE_DRIVER');
        $cache_table_exist = Schema::hasTable('cache');

        if ($cache_driver == 'database' && $cache_table_exist) {
            $this->call('cache:clear');
        }

        if ($cache_driver == 'file') {
            $this->call('cache:clear');
        }

        return 0;
    }
}
