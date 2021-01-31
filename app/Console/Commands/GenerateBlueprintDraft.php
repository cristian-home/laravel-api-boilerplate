<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class GenerateBlueprintDraft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        'blueprint:generatedraft' .
        ' {--I|input=models.json : Json file with models}' .
        ' {--O|output=draft.yaml : draft yaml ouput file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate blueprint draft from json';

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
        $input_file_name = $this->option('input');
        $output_file_name = $this->option('output');

        $input_file_path = Str::startsWith($input_file_name, '/')
            ? $input_file_name
            : base_path($input_file_name);

        $output_file_path = Str::startsWith($output_file_name, '/')
            ? $output_file_name
            : base_path($output_file_name);

        if (!file_exists($input_file_path)) {
            $this->error("{$input_file_name} file does not exist!");
            $this->line("{$input_file_path} file was not found");
            return 0;
        }

        $json = file_get_contents($input_file_path);

        $models = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $controllers = [];
        $seeders = [];

        foreach ($models as $model => $value) {
            $variable = Str::camel(Str::plural($model));
            $controllers[$model] = [
                'resource' => 'api',
                'index' => [
                    'query' => 'all',
                    'resource' => 'paginate:' . $variable,
                ],
            ];
            array_push($seeders, $model);
        }

        $blueprint = [
            'models' => $models,
            'controllers' => $controllers,
            'seeders' => implode(', ', $seeders),
        ];

        $yaml = Yaml::dump($blueprint, 6, 2);

        if (file_exists($output_file_path)) {
            if (
                !$this->confirm(
                    "The {$output_file_name} file already exists, do you want to overwrite it?",
                )
            ) {
                return 0;
            }
        }

        file_put_contents($output_file_path, $yaml, LOCK_EX);

        $this->info(
            '✅ ' .
                $output_file_name .
                ' file successfully created, you can now run the php artisan blueprint:build command.',
        );

        return 0;
    }
}
