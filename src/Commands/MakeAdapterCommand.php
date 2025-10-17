<?php

namespace Heyosseus\LaravelPatternMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeAdapterCommand extends Command
{
  protected $signature = 'pattern:adapter 
                          {name : The name of the adapter class} 
                          {adaptee=Adaptee : The class to be adapted (Model, Service, Repository, or full class path)} 
                          {--namespace=App\\Patterns\\Adapter : The namespace for the adapter}';
  protected $description = 'Generate an Adapter design pattern class with interface in separate files.';

  public function handle()
  {
    $name = $this->argument('name');
    $adaptee = $this->argument('adaptee');
    $namespace = $this->option('namespace');

    $filesystem = new Filesystem();

    // Determine adaptee full class name and import
    $adapteeImport = $this->determineAdapteeImport($adaptee);

    // Create interface
    $this->createInterface($filesystem, $name, $namespace);

    // Create adapter class
    $this->createAdapter($filesystem, $name, $adaptee, $adapteeImport, $namespace);

    $this->info("✅ Adapter pattern created successfully!");
    $this->line("   - Interface: app/Patterns/Adapter/{$name}Interface.php");
    $this->line("   - Adapter: app/Patterns/Adapter/{$name}.php");

    return 0;
  }

  protected function createInterface($filesystem, $name, $namespace)
  {
    $interfaceStub = __DIR__ . '/../stubs/adapter/adapter-interface.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Adapter/{$name}Interface.php");

    if (!$filesystem->exists($interfaceStub)) {
      $this->error("Interface stub not found: {$interfaceStub}");
      return 1;
    }

    $stub = $filesystem->get($interfaceStub);
    $stub = str_replace(
      ['{{ namespace }}', '{{ class }}'],
      [$namespace, $name],
      $stub
    );

    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createAdapter($filesystem, $name, $adaptee, $adapteeImport, $namespace)
  {
    $adapterStub = __DIR__ . '/../stubs/adapter/adapter-class.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Adapter/{$name}.php");

    if (!$filesystem->exists($adapterStub)) {
      $this->error("Adapter stub not found: {$adapterStub}");
      return 1;
    }

    $stub = $filesystem->get($adapterStub);
    $stub = str_replace(
      ['{{ namespace }}', '{{ class }}', '{{ adaptee }}', '{{ adapteeImport }}'],
      [$namespace, $name, $adaptee, $adapteeImport],
      $stub
    );

    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function determineAdapteeImport($adaptee)
  {
    // If it contains namespace separator, use as-is (full class path provided)
    if (Str::contains($adaptee, '\\')) {
      return $adaptee;
    }

    // Common Laravel classes mapping
    $commonClasses = [
      'User' => 'App\\Models\\User',
    ];

    if (isset($commonClasses[$adaptee])) {
      return $commonClasses[$adaptee];
    }

    // Check if it looks like a service (ends with Service)
    if (Str::endsWith($adaptee, 'Service')) {
      return "App\\Services\\{$adaptee}";
    }

    // Check if it looks like a repository (ends with Repository)
    if (Str::endsWith($adaptee, 'Repository')) {
      return "App\\Repositories\\{$adaptee}";
    }

    // Default to App\Models namespace for other cases
    return "App\\Models\\{$adaptee}";
  }
}
