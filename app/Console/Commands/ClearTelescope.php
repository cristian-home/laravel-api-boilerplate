<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ClearTelescope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telescope-app:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Laravel telescope entries.';

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
        if (Schema::hasTable('telescope_entries')) {
            $this->call('telescope:clear');
        }

        return 0;
    }
}
