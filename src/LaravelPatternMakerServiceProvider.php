<?php

namespace Heyosseus\LaravelPatternMaker;

use Illuminate\Support\ServiceProvider;
use Heyosseus\LaravelPatternMaker\Commands\MakeAdapterCommand;
use Heyosseus\LaravelPatternMaker\Commands\MakeStrategyCommand;
use Heyosseus\LaravelPatternMaker\Commands\MakeDecoratorCommand;
use Heyosseus\LaravelPatternMaker\Commands\MakeFactoryCommand;

class LaravelPatternMakerServiceProvider extends ServiceProvider
{
  public function register() {}

  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        MakeAdapterCommand::class,
        MakeStrategyCommand::class,
        MakeDecoratorCommand::class,
        MakeFactoryCommand::class,
      ]);
    }

    $this->publishes([
      __DIR__ . '/stubs' => $this->app->basePath('stubs/patterns'),
    ], 'stubs');
  }
}
