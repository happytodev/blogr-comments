<?php

namespace Happytodev\BlogrComments;

use Filament\Facades\Filament;
use Filament\PanelRegistry;
use Happytodev\Blogr\Services\ExtensionRegistry;
use Happytodev\BlogrComments\Console\Commands\SendCommentDigest;
use Happytodev\BlogrComments\Filament\Pages\CommentSettings;
use Happytodev\BlogrComments\Http\Middleware\ThrottleComments;
use Happytodev\BlogrComments\Models\Comment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class BlogrCommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blogr-comments.php', 'blogr-comments');

        $this->app->singleton(BlogrCommentsPlugin::class, fn () => new BlogrCommentsPlugin);

        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $registry) {
            $panel = $registry->get('admin');

            if (! $panel) {
                return;
            }

            $panel->plugin(new BlogrCommentsPlugin);
        });
    }

    public function boot(): void
    {
        $this->registerExtensions();

        if (! $this->isExtensionEnabled()) {
            return;
        }

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

        $this->registerBladeStacks();
        $this->registerBlogCardCommentCounts();
        $this->registerFilamentPages();
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

            if (config('blogr-comments.display.show_comment_count_on_articles', true)) {
                $commentCount = Comment::where('post_slug', $post->slug)
                    ->where('status', 'approved')
                    ->count();

                if ($commentCount > 0) {
                    $badge = sprintf(
                        '<a href="#comments" class="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-[var(--color-primary)] dark:hover:text-[var(--color-primary-dark)] transition-colors" title="%s">'
                        . '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                        . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>'
                        . '</svg><span>%d</span></a>',
                        __('blogr-comments::messages.comments'),
                        $commentCount
                    );

                    $view->getFactory()->startPush('blogr-post-article-meta', $badge);
                }
            }
        });
    }

    protected function registerBlogCardCommentCounts(): void
    {
        if (! $this->isExtensionEnabled()) {
            return;
        }

        if (! config('blogr-comments.display.show_comment_count_on_cards', true)) {
            return;
        }

        $views = ['blogr::blog.index', 'blogr::blog.category', 'blogr::blog.tag'];

        foreach ($views as $viewName) {
            $this->app['view']->composer($viewName, function (View $view) {
                $posts = $view->getData()['posts'] ?? collect();

                if ($posts->isEmpty()) {
                    return;
                }

                $slugs = $posts->pluck('slug');
                $counts = Comment::whereIn('post_slug', $slugs)
                    ->where('status', 'approved')
                    ->groupBy('post_slug')
                    ->selectRaw('post_slug, count(*) as count')
                    ->pluck('count', 'post_slug');

                foreach ($posts as $post) {
                    $post->comment_count = (int) ($counts[$post->slug] ?? 0);
                }
            });
        }
    }

    protected function registerFilamentPages(): void
    {
        if (! $this->isExtensionEnabled()) {
            return;
        }

        if (! class_exists(Filament::class)) {
            return;
        }

        try {
            $panel = Filament::getPanel('admin');
        } catch (\Exception $e) {
            return;
        }

        if (! $panel) {
            return;
        }

        $panel->pages([CommentSettings::class]);

        Livewire::component(
            app(ComponentRegistry::class)->getName(CommentSettings::class),
            CommentSettings::class,
        );

        $slug = CommentSettings::getSlug($panel);
        $path = trim($panel->getPath(), '/') . '/' . ltrim($slug, '/');
        $middleware = array_merge($panel->getMiddleware(), $panel->getAuthMiddleware());

        Route::get($path, CommentSettings::class)
            ->middleware($middleware)
            ->name('filament.' . $panel->getId() . '.pages.' . $slug);
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('throttle.comments', ThrottleComments::class);
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SendCommentDigest::class]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $frequency = config('blogr-comments.notifications.admin_frequency', 'immediate');

            if ($frequency === 'daily') {
                $time = config('blogr-comments.notifications.digest_time', '09:00');
                [$hour, $minute] = explode(':', $time);
                $schedule->command('blogr-comments:send-digest', ['--period' => 'daily'])
                    ->dailyAt(sprintf('%02d:%02d', (int) $hour, (int) $minute));
            } elseif ($frequency === 'weekly') {
                $time = config('blogr-comments.notifications.digest_time', '09:00');
                $day = config('blogr-comments.notifications.digest_day', 'monday');
                [$hour, $minute] = explode(':', $time);
                $schedule->command('blogr-comments:send-digest', ['--period' => 'weekly'])
                    ->weeklyOn($day, sprintf('%02d:%02d', (int) $hour, (int) $minute));
            }
        });
    }

    protected function isExtensionEnabled(): bool
    {
        if (! $this->app->has(ExtensionRegistry::class)) {
            return true;
        }

        return app(ExtensionRegistry::class)->isEnabled('blogr-comments');
    }
}
