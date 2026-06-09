<?php

namespace Happytodev\BlogrComments\Filament\Widgets;

use Filament\Widgets\Widget;
use Happytodev\BlogrComments\Models\Comment;

class PendingCommentsWidget extends Widget
{
    protected static string $view = 'blogr-comments::filament.widgets.pending-comments';

    protected int|string|array $columnSpan = 1;

    public function getPendingCount(): int
    {
        return Comment::where('status', 'pending')->count();
    }

    public function getPendingUrl(): string
    {
        return '/admin/comments';
    }
}
