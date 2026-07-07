<?php

use Happytodev\BlogrComments\Filament\Pages\CommentSettings;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ListComments;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ViewComment;
use Illuminate\Support\Str;
use Livewire\Livewire;

test('all page components can be instantiated', function () {
    $components = [
        CommentSettings::class,
        ListComments::class,
        ViewComment::class,
    ];

    foreach ($components as $component) {
        expect(fn () => app($component))->not->toThrow(\Throwable::class);
    }
});
