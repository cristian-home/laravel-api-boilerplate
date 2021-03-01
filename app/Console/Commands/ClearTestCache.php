<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function PHPUnit\Framework\fileExists;

class ClearTestCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CLear php unit test cache';

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
        $file = './.phpunit.result.cache';

        if (file_exists($file)) {
            unlink($file);
        }

        $this->info('PHP Unit cache have been cleared!');

        return 0;
    }
}
