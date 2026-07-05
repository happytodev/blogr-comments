<?php

namespace Happytodev\BlogrComments\Tests;

use Filament\FilamentServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \Happytodev\BlogrComments\BlogrCommentsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:flGutZu+1SkSKSy00lcwBG+SX/goiY4fLlwW8rGbt3U=');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('livewire.class_namespace', 'App\\Livewire');
    }
}
