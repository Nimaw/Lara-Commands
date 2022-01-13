<?php

namespace Nimaw\LaraCommands\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\{InputArgument, InputOption};
use Nimaw\LaraCommands\Parsers\{FileGenerator, GenerateFile};
use InvalidArgumentException;

class ServiceMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'service';


    /**
     * Get Command argumant EX : HasAuth
     * getArguments
     *
     */
    protected function getArguments(): array
    {
        return [
            ['service', InputArgument::REQUIRED, 'The name of the service'],
        ];
    }


    /**
     * Get the stub file .
     */
    protected function getStub(): string
    {
        $stub = null;
        if ($this->option('model')) {
            $stub = '/stubs/service.model.stub';
        }

        $stub = $stub ?? '/stubs/service.stub';

        return $this->resolveStubPath($stub);
    }

    /**
     * Resolve the full path to the stub.
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : $stub;
    }


    /**
     * getServiceName
     */
    private function getServiceName(): string
    {
        $service = $service = Str::studly($this->argument('service'));
        return $service;
    }

    /**
     * getFilePath
     */
    protected function getFilePath(): string
    {
        if (Str::contains(strtolower($service = $this->getServiceName()), '.php') === false) {
            $service .= '.php';
        }
        return app_path('Services/' . $service);
    }

    /**
     * getServiceNameWithoutNamespace
     *
     */
    private function getServiceNameWithoutNamespace()
    {
        return class_basename($this->getServiceName());
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    protected function getReplacements()
    {
        $replacments = null;

        if ($this->option('model')) {
            $modelClass = $this->parseModel($this->option('model'));
            if (!class_exists($modelClass)) {
                if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                    $this->call('make:model', ['name' => $modelClass]);
                }
            }

            $replacments = [
                'MODEL' => $this->option('model'),
                'MODEL_NAMESPACE' => $modelClass
            ];
        }
        return $replacments;
    }

    /**
     * getServiceContents
     */
    protected function getServiceContents(): mixed
    {
        return (new GenerateFile(
            __DIR__ . $this->getStub(),
            array_merge([
                'NAMESPACE' => $this->getClassNamespace(),
                'CLASS' => $this->getServiceNameWithoutNamespace(),

            ], $this->getReplacements())
        ))->render();
    }


    public function handle()
    {
        $path = str_replace('\\', '/', $this->getFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getServiceContents();

        try {
            (new FileGenerator('Service', $path, $contents))->generate();
            $this->info("Service created successfully.");
        } catch (\Exception $e) {
            $this->error("{$e->getMessage()}");
        }

        return;
    }

    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }
    public function getClassNamespace()
    {
        $extra = str_replace($this->getServiceNameWithoutNamespace(), '', $this->getServiceName());
        $extra = str_replace('/', '\\', $extra);
        $namespace =  $this->getDefaultNamespace('app\Services');
        $namespace .= '\\' . $extra;
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = trim($namespace, '\\');
        return empty($namespace) ? 'app\Services' : $namespace;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'Generate a service for the given model.'],
        ];
    }
}
