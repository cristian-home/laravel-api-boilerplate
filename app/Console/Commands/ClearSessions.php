<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ClearSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CLear app sessions';

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
        $file_list = glob('./storage/framework/sessions/{,*/}*', GLOB_BRACE);

        foreach ($file_list as $key => $file) {
            $this->comment("Removing $file");
            unlink($file);
        }

        if (Schema::hasTable('sessions')) {
            $this->comment('Deleting records from the sessions table');
            DB::table('sessions')->truncate();
        }

        return 0;
    }
}
