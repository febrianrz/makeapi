<?php

namespace Febrianrz\Makeapi;


use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ApiMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = "make:api";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make API Resources';

    private $modelName;
    private $pluralName;
    private $pluralDashName;
    private $tableName;
    private $variableName;

    private $types = [
        'controller',
        'factory',
        'migration',
        'model',
        'policy',
        'request',
        'seeder',
        'test',
        'test.unauthorized',
    ];

    private $paths = [];

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the Model'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['x-controller', 'c', InputOption::VALUE_NONE, 'Create an API without new controller for the model'],

            ['x-factory', 'f', InputOption::VALUE_NONE, 'Create an API without new factory for the model'],

            // phpcs:ignore
            ['x-migration', 'm', InputOption::VALUE_NONE, 'Create an API without new migration file for the model'],

            ['x-model', 'M', InputOption::VALUE_NONE, 'Create an API without new migration file for the model'],

            ['x-policy', 'p', InputOption::VALUE_NONE, 'Create an API without new policy file for the model'],

            ['x-request', 'r', InputOption::VALUE_NONE, 'Create an API without new request file for the model'],

            ['x-seeder', 's', InputOption::VALUE_NONE, 'Create an API without new seeder file for the model'],

            ['x-test', 't', InputOption::VALUE_NONE, 'Create an API without new test file for the model'],

            // phpcs:ignore
            ['x-test.unauthorized', 'u', InputOption::VALUE_NONE, 'Create an API without new unauthorized test file for the model'],

            ['pretend', 'P', InputOption::VALUE_NONE, 'Dump the code that would be created'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->modelName = $this->argument('name');
        $this->snakeName = strtolower(Str::snake($this->modelName));
        $this->pluralName = Str::plural($this->modelName);
        $this->pluralDashName = str_replace('_', '-', Str::snake($this->pluralName));
        $this->tableName = Str::snake($this->pluralName);
        $this->variableName = Str::camel($this->modelName);
        $this->titleName = Str::title(str_replace('_', ' ', Str::snake($this->modelName)));

        $this->initPaths();
        $this->checkFolderExist();
        // dd( $this->paths);

        foreach ($this->types as $type) {
            if (!$this->option("x-{$type}")) {
                $this->make($type);
            }
        }

        if (!$this->option('x-seeder')) {
            $this->appendDatabaseSeeder();
        }

        if (!$this->option('x-controller')) {
            $this->appendRoute();
        }

        if (!$this->option('x-migration') || !$this->option('x-seeder') || !$this->option('x-factory')) {
            $this->composerDumpAutoload();
        }
    }

    public function initPaths()
    {
        $date = date('Y_m_d_His');

        $this->paths['migration'] = "migrations/{$date}_create_{$this->tableName}_table.php";
        $this->paths['factory'] = "factories/{$this->modelName}Factory.php";
        $this->paths['seeder'] = "seeds/{$this->pluralName}Seeder.php";
        $this->paths['model'] = "app/{$this->modelName}.php";
        $this->paths['policy'] = "app/Policies/{$this->modelName}Policy.php";
        $this->paths['request'] = "app/Http/Requests/{$this->modelName}Request.php";
        $this->paths['test'] = "tests/Feature/Api/{$this->modelName}Test.php";
        $this->paths['test.unauthorized'] = "tests/Feature/Api/{$this->modelName}UnauthorizedTest.php";
        $this->paths['controller'] = "app/Http/Controllers/Api/{$this->modelName}Controller.php";
    }

    public function make($type)
    {
        $template = str_replace(
            [
                'ModelName',
                'SnakeName',
                'PluralName',
                'PluralDashName',
                'TableName',
                'VariableName',
                'TitleName',
            ],
            [
                $this->modelName,
                $this->snakeName,
                $this->pluralName,
                $this->pluralDashName,
                $this->tableName,
                $this->variableName,
                $this->titleName,
            ],
            $this->getStub($type)
        );

        if (!$this->option('pretend')) {
            file_put_contents($this->getOutputPath($type), $template);

            $this->comment(Str::title($type) . '<info> created: </info>' . $this->paths[$type]);
        } else {
            $this->comment('<info>This </info>' . Str::title($type) . '<info> code would be written in: </info>'
                . $this->paths[$type]);
            if ($this->getOutput()->getVerbosity() == OutputInterface::VERBOSITY_VERBOSE) {
                $this->line($template);
            }
        }
    }

    public function appendDatabaseSeeder()
    {
        $path = 'seeds/DatabaseSeeder.php';
        $filepath = $this->laravel->databasePath($path);
        $code = "{$this->pluralName}Seeder::class,
            /* make:api New Seeder */";

        $fileContent = str_replace('/* make:api New Seeder */', $code, file_get_contents($filepath));

        $this->line('');

        if (!$this->option('pretend')) {
            file_put_contents($filepath, $fileContent);

            $this->comment('New Seeder has been appended to <info>database/seeds/DatabaseSeeder.php</info>');
        } else {
            $this->comment('<info>This </info>Seeder<info> code would be appended in: </info>' . $path);
            $this->line("            {$code}");
        }
    }

    public function appendRoute()
    {
        $path = 'routes/api.php';
        $filepath = $this->laravel->basePath($path);
        $code = "Route::apiResource('/{$this->pluralDashName}', '{$this->modelName}Controller');
    /* make:api New Route */";

        $fileContent = str_replace('/* make:api New Route */', $code, file_get_contents($filepath));

        $this->line('');

        if (!$this->option('pretend')) {
            file_put_contents($filepath, $fileContent);

            $this->comment('New Route has been appended to <info>routes/api.php</info>');
        } else {
            $this->comment('<info>This </info>Route<info> code would be appended in: </info>' . $path);
            $this->line("    {$code}");
        }
    }

    public function composerDumpAutoload()
    {
        $this->line('');

        if (!$this->option('pretend')) {
            $this->info('Composer dump the autoload');

            $this->laravel['composer']->dumpAutoloads();
            $this->call('optimize:clear');
        } else {
            $this->info('Composer will dump the autoload');
        }
    }

    protected function getStub($type)
    {
        
        if ($type == 'seeder' && !$this->option('x-factory')) {
            $type = 'seeder.factory';
        }

        if ($type == 'controller') {
            if ($this->option('x-request')) {
                $type = 'controller.api';
            } else {
                $type = 'controller.request.api';
            }
        }

        return file_get_contents(dirname(__FILE__).("/stubs/$type.stub"));
    }

    protected function getOutputPath($type)
    {
        switch ($type) {
            case 'factory':
            case 'migration':
            case 'seeder':
                return $this->laravel->databasePath($this->paths[$type]);

            case 'controller':
            case 'model':
            case 'policy':
            case 'request':
            case 'test':
            case 'test.unauthorized':
                return $this->laravel->basePath($this->paths[$type]);
        }
    }

    protected function checkFolderExist(){
        $paths = [
            app_path()."/Http/Controllers/Api",
            app_path()."/Policies",
            app_path()."/Http/Requests",
            base_path()."/tests/Feature/Api",
        ];
        // dd($paths);
        foreach($paths as $path){
            if(!File::exists($path)){
                // dd($path);
                mkdir($path);
            }
        }
        
    }
}
