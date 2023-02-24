<?php

namespace ProfilerDeep\Providers;

use Illuminate\Support\ServiceProvider;
use ProfilerDeep\Console\Commands\ExampleCommand;
use ProfilerDeep\Console\Commands\MakePackage;

class ProfilerDeepServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                ExampleCommand::class,
                
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-profiler-deep');


        $migrations_path = __DIR__ . '/../copy/migrations';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => database_path('migrations'),
            ], 'public');
        }

        $migrations_path = __DIR__ . '/../copy/Controllers';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => app_path('Http/Controllers/ProfilerDeep'),
            ], 'public');
        }

        $migrations_path = __DIR__ . '/../copy/views';
        if (file_exists($migrations_path)) {
            $this->publishes([
                $migrations_path => resource_path('views/laravel-profiler-deep'),
            ], 'public');
        }


        $js_path = __DIR__ . '/../copy/js';
        if (file_exists($js_path)) {
            $this->publishes([
                $js_path => public_path('js/laravel-profiler-deep'),
            ], 'public');
        }

        /*
        $this->publishes([
            __DIR__ . '/../copy/Controllers/ProfilerDeep' => app_path('Http/Controllers'),
        ], 'public');
*/

    }
}
