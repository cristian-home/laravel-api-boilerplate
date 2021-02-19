<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CLear all Laravel logs';

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
        $file_list = glob(
            './storage/{,*/,*/*/,*/*/*/,*/*/*/*/}*.log',
            GLOB_BRACE,
        );

        foreach ($file_list as $key => $file) {
            $this->comment("Removing $file");
            exec('rm ' . $file);
        }

        $this->info('Logs have been cleared!');

        return 0;
    }
}
