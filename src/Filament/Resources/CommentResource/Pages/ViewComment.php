<?php

namespace Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Happytodev\BlogrComments\Filament\Resources\CommentResource;

class ViewComment extends ViewRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label(__('blogr-comments::messages.approve'))
                ->color('success')
                ->action(fn () => $this->record->update(['status' => 'approved']))
                ->visible(fn () => $this->record->status !== 'approved'),
            Actions\Action::make('reject')
                ->label(__('blogr-comments::messages.reject'))
                ->color('danger')
                ->action(fn () => $this->record->update(['status' => 'rejected']))
                ->visible(fn () => $this->record->status !== 'rejected'),
            Actions\Action::make('spam')
                ->label(__('blogr-comments::messages.mark_spam'))
                ->color('gray')
                ->action(fn () => $this->record->update(['status' => 'spam']))
                ->visible(fn () => $this->record->status !== 'spam'),
        ];
    }
}
