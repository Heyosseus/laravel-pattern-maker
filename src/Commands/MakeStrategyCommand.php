<?php

namespace Heyosseus\LaravelPatternMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeStrategyCommand extends Command
{
  protected $signature = 'pattern:strategy 
                          {name : The name of the strategy context class} 
                          {strategies?* : Optional strategy names to generate}
                          {--namespace=App\\Patterns\\Strategy : The namespace for the strategy}';
  protected $description = 'Generate a Strategy design pattern with context and strategy classes.';

  public function handle()
  {
    $name = $this->argument('name');
    $strategies = $this->argument('strategies') ?? [];
    $namespace = $this->option('namespace');

    $filesystem = new Filesystem();

    // Create strategy interface
    $this->createStrategyInterface($filesystem, $name, $namespace);

    // Create context class
    $this->createContext($filesystem, $name, $namespace);

    // Create concrete strategies if provided
    foreach ($strategies as $strategy) {
      $this->createConcreteStrategy($filesystem, $name, $strategy, $namespace);
    }

    $this->info("✅ Strategy pattern created successfully!");
    $this->line("   - Interface: app/Patterns/Strategy/{$name}StrategyInterface.php");
    $this->line("   - Context: app/Patterns/Strategy/{$name}Context.php");

    if (count($strategies) > 0) {
      foreach ($strategies as $strategy) {
        $this->line("   - Strategy: app/Patterns/Strategy/{$strategy}.php");
      }
    } else {
      $this->comment("   💡 Tip: Add concrete strategies with: php artisan pattern:strategy {$name} ConcreteStrategyName");
    }

    return 0;
  }

  protected function createStrategyInterface($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/strategy/strategy-interface.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Strategy/{$name}StrategyInterface.php");

    if (!$filesystem->exists($stubPath)) {
      $this->error("Strategy interface stub not found: {$stubPath}");
      return 1;
    }

    $stub = $filesystem->get($stubPath);
    $stub = str_replace(
      ['{{ namespace }}', '{{ class }}'],
      [$namespace, $name],
      $stub
    );

    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createContext($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/strategy/strategy-context.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Strategy/{$name}Context.php");

    if (!$filesystem->exists($stubPath)) {
      $this->error("Strategy context stub not found: {$stubPath}");
      return 1;
    }

    $stub = $filesystem->get($stubPath);
    $stub = str_replace(
      ['{{ namespace }}', '{{ class }}'],
      [$namespace, $name],
      $stub
    );

    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createConcreteStrategy($filesystem, $name, $strategy, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/strategy/strategy-concrete.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Strategy/{$strategy}.php");

    if (!$filesystem->exists($stubPath)) {
      $this->error("Concrete strategy stub not found: {$stubPath}");
      return 1;
    }

    $stub = $filesystem->get($stubPath);
    $stub = str_replace(
      ['{{ namespace }}', '{{ class }}', '{{ interface }}'],
      [$namespace, $strategy, $name],
      $stub
    );

    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }
}
