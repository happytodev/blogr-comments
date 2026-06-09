<?php

namespace Happytodev\BlogrComments\Filament\Resources;

use Filament\Resources\Resource;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ListComments;
use Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages\ViewComment;
use Happytodev\BlogrComments\Models\Comment;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Comments';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => ListComments::route('/'),
            'view' => ViewComment::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Comment::where('status', 'pending')->count();
    }
}
