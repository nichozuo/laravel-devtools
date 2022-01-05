<?php


namespace Nichozuo\LaravelDevtools;


use Nichozuo\LaravelDevtools\Commands\BackupMenu;
use Nichozuo\LaravelDevtools\Commands\DumpTableCommand;
use Nichozuo\LaravelDevtools\Commands\GenFilesCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->commands([
            DumpTableCommand::class,
            GenFilesCommand::class,
            BackupMenu::class,
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/resources/laravel-doc-react/dist' => public_path('docs'),
            __DIR__ . '/resources/laravel-devtools' => resource_path('laravel-devtools'),
            __DIR__ . '/resources/config/nichozuo.php' => config_path('nichozuo.php')
        ]);
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
    }
}