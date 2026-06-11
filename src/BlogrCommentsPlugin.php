<?php

namespace Happytodev\BlogrComments;

use Filament\Contracts\Plugin as FilamentPlugin;
use Filament\Panel;
use Happytodev\Blogr\Contracts\BlogrExtension;
use Happytodev\BlogrComments\Filament\Pages\CommentSettings;
use Happytodev\BlogrComments\Filament\Resources\CommentResource;

class BlogrCommentsPlugin implements BlogrExtension, FilamentPlugin
{
    public function getId(): string
    {
        return 'blogr-comments';
    }

    public function getName(): string
    {
        return 'Comments';
    }

    public function getDescription(): string
    {
        return 'Threaded comments with moderation, voting, anti-spam, and email notifications.';
    }

    public function getVersion(): string
    {
        return '1.1.0';
    }

    public function getAuthor(): string
    {
        return 'HappyToDev';
    }

    public function getHomepage(): ?string
    {
        return 'https://github.com/happytodev/blogr-comments';
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CommentResource::class,
        ])->pages([
            CommentSettings::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
