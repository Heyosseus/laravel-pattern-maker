<?php

namespace Heyosseus\LaravelPatternMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeObserverCommand extends Command
{
  protected $signature = 'pattern:observer {name : The name of the subject class} {observers?* : Optional observer class names} {--namespace=App\\Patterns\\Observer : The namespace for the observer}';
  protected $description = 'Generate an Observer design pattern with subject, observer interface, and concrete observers.';

  public function handle()
  {
    $name = $this->argument('name');
    $observers = $this->argument('observers') ?? [];
    $namespace = $this->option('namespace');
    $filesystem = new Filesystem();

    // Create observer interface
    $this->createObserverInterface($filesystem, $name, $namespace);
    // Create subject class
    $this->createSubject($filesystem, $name, $namespace);
    // Create concrete observers
    foreach ($observers as $observer) {
      $this->createConcreteObserver($filesystem, $name, $observer, $namespace);
    }

    $this->info("✅ Observer pattern created successfully!");
    $this->line("   - Observer Interface: app/Patterns/Observer/{$name}ObserverInterface.php");
    $this->line("   - Subject: app/Patterns/Observer/{$name}Subject.php");
    if (count($observers) > 0) {
      foreach ($observers as $observer) {
        $this->line("   - Observer: app/Patterns/Observer/{$observer}.php");
      }
    } else {
      $this->comment("   💡 Tip: Add observers with: php artisan pattern:observer {$name} EmailObserver LogObserver");
    }
    return 0;
  }

  protected function createObserverInterface($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/observer/observer-interface.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Observer/{$name}ObserverInterface.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Observer interface stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createSubject($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/observer/observer-subject.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Observer/{$name}Subject.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Observer subject stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createConcreteObserver($filesystem, $name, $observer, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/observer/observer-concrete.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Observer/{$observer}.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Observer stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}', '{{ subject }}'], [$namespace, $observer, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }
}
