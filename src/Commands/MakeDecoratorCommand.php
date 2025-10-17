<?php

namespace Heyosseus\LaravelPatternMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeDecoratorCommand extends Command
{
  protected $signature = 'pattern:decorator {name : The name of the base class} {decorators?* : Optional decorator class names} {--namespace=App\\Patterns\\Decorator : The namespace for the decorator}';
  protected $description = 'Generate a Decorator design pattern with base, interface, and decorator classes.';

  public function handle()
  {
    $name = $this->argument('name');
    $decorators = $this->argument('decorators') ?? [];
    $namespace = $this->option('namespace');
    $filesystem = new Filesystem();

    // Create interface
    $this->createInterface($filesystem, $name, $namespace);
    // Create base class
    $this->createBase($filesystem, $name, $namespace);
    // Create decorators
    foreach ($decorators as $decorator) {
      $this->createDecorator($filesystem, $name, $decorator, $namespace);
    }

    $this->info("✅ Decorator pattern created successfully!");
    $this->line("   - Interface: app/Patterns/Decorator/{$name}ComponentInterface.php");
    $this->line("   - Base: app/Patterns/Decorator/{$name}Component.php");
    if (count($decorators) > 0) {
      foreach ($decorators as $decorator) {
        $this->line("   - Decorator: app/Patterns/Decorator/{$decorator}.php");
      }
    } else {
      $this->comment("   💡 Tip: Add decorators with: php artisan pattern:decorator {$name} LoggingDecorator CachingDecorator");
    }
    return 0;
  }

  protected function createInterface($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/decorator-interface.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Decorator/{$name}ComponentInterface.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Decorator interface stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createBase($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/decorator-base.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Decorator/{$name}Component.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Decorator base stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createDecorator($filesystem, $name, $decorator, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/decorator-decorator.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Decorator/{$decorator}.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Decorator stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}', '{{ base }}'], [$namespace, $decorator, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }
}
