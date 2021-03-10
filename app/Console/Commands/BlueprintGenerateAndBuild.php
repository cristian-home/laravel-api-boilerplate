<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class BlueprintGenerateAndBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        'blueprint:generate-and-build ' .
        '{--i|input=models.json : Json file with models} ' .
        '{--o|output=draft.yaml : draft yaml ouput file} ' .
        '{--force : run without asking for confirmation} ' .
        '{draft? : The path to the draft file, default: draft.yaml or draft.yml } ' .
        '{--only= : Comma separated list of file classes to generate, skipping the rest } ' .
        '{--skip= : Comma separated list of file classes to skip, generating the rest } ' .
        '{--m|overwrite-migrations : Update existing migration files, if found }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate blueprint draft from json and build from draft';

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
        $this->call('blueprint:generate', [
            '--input' => $this->option('input'),
            '--output' => $this->option('output'),
            '--force' => $this->option('force'),
        ]);

        $this->call('blueprint:build', [
            'draft' => $this->argument('draft'),
            '--only' => $this->option('only'),
            '--skip' => $this->option('skip'),
            '--overwrite-migrations' => $this->option('overwrite-migrations'),
        ]);

        return 0;
    }
}
