<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaSortable5;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class SortableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/frontbyte-nova-sortable.php', 'frontbyte-nova-sortable');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/frontbyte-nova-sortable.php' => config_path('frontbyte-nova-sortable.php'),
        ], 'frontbyte-nova-sortable-config');

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-sortable', __DIR__ . '/../dist/js/sortable.js');
            Nova::style('nova-sortable', __DIR__ . '/../dist/style.css');
        });

        $this->app->booted(function () {
            $this->routes();
        });
    }

    protected function routes(): void
    {
        Route::middleware(['web', 'auth:' . implode(',', config('frontbyte-nova-sortable.guards', ['web']))])
            ->prefix('nova-vendor/nova-sortable')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
