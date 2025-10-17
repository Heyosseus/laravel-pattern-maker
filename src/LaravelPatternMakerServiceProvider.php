<?php

namespace Heyosseus\LaravelPatternMaker;

use Illuminate\Support\ServiceProvider;
use Heyosseus\LaravelPatternMaker\Commands\MakeAdapterCommand;

class LaravelPatternMakerServiceProvider extends ServiceProvider
{
  public function register() {}

  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        MakeAdapterCommand::class,
      ]);
    }

    $this->publishes([
      __DIR__ . '/stubs' => $this->app->basePath('stubs/patterns'),
    ], 'stubs');
  }
}
