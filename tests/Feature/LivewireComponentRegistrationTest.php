<?php

use Happytodev\BlogrComments\Filament\Pages\CommentSettings;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ListComments;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ViewComment;
use Livewire\Mechanisms\ComponentRegistry;

test('all page components are resolvable from Livewire ComponentRegistry', function () {
    $components = [
        CommentSettings::class,
        ListComments::class,
        ViewComment::class,
    ];

    foreach ($components as $component) {
        $name = app(ComponentRegistry::class)->getName($component);
        $resolved = app(ComponentRegistry::class)->getClass($name);
        expect($resolved)->toBe($component);
    }
});
