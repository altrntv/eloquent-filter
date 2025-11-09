<?php

namespace Altrntv\EloquentFilter;

use Illuminate\Support\ServiceProvider;

class EloquentFilterProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->configurePaths();
    }

    private function configurePaths(): void
    {
        $this->publishes([
            __DIR__ . '../config/eloquent-filter.php' => config_path('eloquent-filter.php'),
        ]);
    }
}
