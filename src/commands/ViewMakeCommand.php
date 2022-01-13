<?php

namespace Nimaw\LaraCommands\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Nimaw\LaraCommands\Parsers\{FileGenerator, GenerateFile};

class ViewMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:view';

    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade view';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'view';


    /**
     * Get Command argumant EX : HasAuth
     * getArguments
     *
     */
    protected function getArguments(): array
    {
        return [
            ['view', InputArgument::REQUIRED, 'The name of the view'],
        ];
    }


    /**
     * Get the stub file .
     */
    protected function getStub(): string
    {
        $stub = '/stubs/view.template.stub';
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
     * getViewName
     */
    private function getViewName(): string
    {
        $view = Str::camel($this->argument('view'));
        if (Str::contains(strtolower($view), '.blade.php') === false) {
            $view .= '.blade.php';
        }
        return $view;
    }

    /**
     * getFilePath
     */
    protected function getFilePath(): string
    {
        return  $this->viewPath($this->getViewName());
    }

    /**
     * getViewContents
     */
    protected function getViewContents(): mixed
    {
        return (new GenerateFile(__DIR__ . $this->getStub()))->render();
    }

    public function handle()
    {
        $path = str_replace('\\', '/', $this->getFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getViewContents();

        try {
            (new FileGenerator('View', $path, $contents))->generate();
            $this->info("View created successfully.");
        } catch (\Exception $e) {
            $this->error("{$e->getMessage()}");
        }

        return;
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
