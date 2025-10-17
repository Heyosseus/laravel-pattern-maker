<?php

namespace Heyosseus\LaravelPatternMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeFactoryCommand extends Command
{
  protected $signature = 'pattern:factory {name : The name of the factory} {products?* : Optional product class names} {--namespace=App\\Patterns\\Factory : The namespace for the factory}';
  protected $description = 'Generate a Factory design pattern with interface, factory, and product classes.';

  public function handle()
  {
    $name = $this->argument('name');
    $products = $this->argument('products') ?? [];
    $namespace = $this->option('namespace');
    $filesystem = new Filesystem();

    // Create interface
    $this->createInterface($filesystem, $name, $namespace);
    // Create factory class
    $this->createFactory($filesystem, $name, $namespace, $products);
    // Create product classes
    foreach ($products as $product) {
      $this->createProduct($filesystem, $product, $namespace, $name);
    }

    $this->info("✅ Factory pattern created successfully!");
    $this->line("   - Interface: app/Patterns/Factory/{$name}FactoryInterface.php");
    $this->line("   - Factory: app/Patterns/Factory/{$name}Factory.php");
    if (count($products) > 0) {
      foreach ($products as $product) {
        $this->line("   - Product: app/Patterns/Factory/{$product}.php");
      }
    } else {
      $this->comment("   💡 Tip: Add products with: php artisan pattern:factory {$name} Car Bike");
    }
    return 0;
  }

  protected function createInterface($filesystem, $name, $namespace)
  {
    $stubPath = __DIR__ . '/../stubs/factory/factory-interface.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Factory/{$name}FactoryInterface.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Factory interface stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createFactory($filesystem, $name, $namespace, $products)
  {
    $stubPath = __DIR__ . '/../stubs/factory/factory-factory.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Factory/{$name}Factory.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Factory class stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $productCases = '';
    foreach ($products as $product) {
      $productCases .= "            case '{$product}':\n                return new {$product}();\n";
    }
    $stub = str_replace([
      '{{ namespace }}',
      '{{ class }}',
      '{{ product_cases }}'
    ], [
      $namespace,
      $name,
      $productCases
    ], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }

  protected function createProduct($filesystem, $product, $namespace, $factoryName)
  {
    $stubPath = __DIR__ . '/../stubs/factory/factory-product.stub';
    $outputPath = $this->laravel->basePath("app/Patterns/Factory/{$product}.php");
    if (!$filesystem->exists($stubPath)) {
      $this->error("Factory product stub not found: {$stubPath}");
      return 1;
    }
    $stub = $filesystem->get($stubPath);
    $stub = str_replace([
      '{{ namespace }}',
      '{{ class }}',
      '{{ factory }}'
    ], [
      $namespace,
      $product,
      $factoryName
    ], $stub);
    $filesystem->ensureDirectoryExists(dirname($outputPath));
    $filesystem->put($outputPath, $stub);
  }
}
