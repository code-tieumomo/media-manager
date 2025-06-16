<?php

namespace MediaManager;

use Illuminate\Support\ServiceProvider;

class MediaManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/media-manager.php' => config_path('media-manager.php'),
        ], 'config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/media-manager'),
        ], 'views');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'media-manager');

        // Register Blade alias for media manager if Livewire is available
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('media-manager', \MediaManager\Http\Livewire\MediaManager::class);
        }

        // Register the media picker as a Blade component
        if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
            \Illuminate\Support\Facades\Blade::component('media-manager::media-picker', 'media-manager::picker');
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/media-manager.php', 'media-manager'
        );
    }
} 