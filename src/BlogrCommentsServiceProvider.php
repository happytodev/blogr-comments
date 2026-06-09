<?php

namespace Happytodev\BlogrComments;

use Filament\PanelRegistry;
use Happytodev\Blogr\Services\ExtensionRegistry;
use Happytodev\BlogrComments\Filament\Widgets\PendingCommentsWidget;
use Happytodev\BlogrComments\Http\Middleware\ThrottleComments;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class BlogrCommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blogr-comments.php', 'blogr-comments');

        $this->app->singleton(BlogrCommentsPlugin::class, fn () => new BlogrCommentsPlugin);

        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $registry) {
            $panel = $registry->get('admin');
            $panel->plugin(new BlogrCommentsPlugin);
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blogr-comments');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'blogr-comments');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->publishes([
            __DIR__ . '/../config/blogr-comments.php' => config_path('blogr-comments.php'),
        ], 'blogr-comments-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/blogr-comments'),
        ], 'blogr-comments-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/blogr-comments'),
        ], 'blogr-comments-lang');

        $this->registerExtensions();
        $this->registerBladeStacks();
        $this->registerMiddleware();
        $this->registerCommands();
    }

    protected function registerExtensions(): void
    {
        if ($this->app->has(ExtensionRegistry::class)) {
            $registry = app(ExtensionRegistry::class);
            $registry->register(new BlogrCommentsPlugin);
        }
    }

    protected function registerBladeStacks(): void
    {
        $this->app['view']->composer('blogr::layouts.blog', function (View $view) {
            if (! $this->isExtensionEnabled()) {
                return;
            }
        });

        $this->app['view']->composer('blogr::blog.show', function (View $view) {
            if (! $this->isExtensionEnabled()) {
                return;
            }

            $post = $view->getData()['post'] ?? null;

            if (! $post) {
                return;
            }

            $commentsView = view('blogr-comments::comments', [
                'postSlug' => $post->slug,
                'comments' => collect([]),
                'sort' => 'newest',
            ])->render();

            $view->getFactory()->startPush('comments', $commentsView);
        });
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('throttle.comments', ThrottleComments::class);
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            //
        }
    }

    protected function isExtensionEnabled(): bool
    {
        if (! $this->app->has(ExtensionRegistry::class)) {
            return true;
        }

        return app(ExtensionRegistry::class)->isEnabled('blogr-comments');
    }
}
