<?php

namespace Nimaw\LaraCommands\commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Nimaw\LaraCommands\Parsers\{FileGenerator, GenerateFile};

class TraitMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:trait';

    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'trait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'trait';


    /**
     * Get Command argumant EX : HasAuth
     * getArguments
     *
     */
    protected function getArguments(): array
    {
        return [
            ['trait', InputArgument::REQUIRED, 'The name of the trait'],
        ];
    }


    /**
     * Get the stub file .
     */
    protected function getStub(): string
    {
        $stub = '/stubs/trait.stub';
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
     * getTraitName
     */
    private function getTraitName(): string
    {
        $trait =  $trait = Str::studly($this->argument('trait'));
        return $trait;
    }

    /**
     * getFilePath
     */
    protected function getFilePath(): string
    {
        if (Str::contains(strtolower($trait = $this->getTraitName()), '.php') === false) {
            $trait .= '.php';
        }
        return  app_path('Traits/' . $trait);
    }

    /**
     * getTraitNameWithoutNamespace
     *
     */
    private function getTraitNameWithoutNamespace()
    {
        return class_basename($this->getTraitName());
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return '';
    }

    /**
     * getTraitContents
     */
    protected function getTraitContents(): mixed
    {
        return (new GenerateFile(__DIR__ . $this->getStub(), [
            'NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getTraitNameWithoutNamespace()
        ]))->render();
    }


    public function handle()
    {
        $path = str_replace('\\', '/', $this->getFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTraitContents();

        try {
            (new FileGenerator('Trait', $path, $contents))->generate();
            $this->info("Trait created successfully.");
        } catch (\Exception $e) {
            $this->error("{$e->getMessage()}");
        }

        return;
    }

    public function getClassNamespace()
    {
        $extra = str_replace($this->getTraitNameWithoutNamespace(), '', $this->getTraitName());
        $extra = str_replace('/', '\\', $extra);
        $namespace =  $this->getDefaultNamespace('app\\');
        $namespace .= '\\' . $extra;
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = trim($namespace, '\\');
        return empty($namespace) ? 'app\Traits' : $namespace;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
