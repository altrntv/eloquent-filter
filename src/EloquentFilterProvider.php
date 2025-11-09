<?php

namespace Altrntv\EloquentFilter;

use Altrntv\EloquentFilter\Commands\EloquentFilterMakeCommand;
use Illuminate\Support\ServiceProvider;

class EloquentFilterProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->configurePaths();

        $this->mergeConfig();

        $this->commands([EloquentFilterMakeCommand::class]);
    }

    private function configurePaths(): void
    {
        $this->publishes([
            __DIR__ . '/../config/eloquent-filter.php' => config_path('eloquent-filter.php'),
        ]);
    }

    private function mergeConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/eloquent-filter.php', 'eloquent-filter');
    }
}
