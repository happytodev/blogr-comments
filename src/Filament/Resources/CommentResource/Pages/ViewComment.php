<?php

namespace Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Happytodev\BlogrComments\Filament\Resources\CommentResource;

class ViewComment extends ViewRecord
{
    protected static string $resource = CommentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('blogr-comments::messages.comment'))
                    ->schema([
                        TextEntry::make('content_html')
                            ->label('')
                            ->html(),
                    ]),
                Section::make(__('blogr-comments::messages.author_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('author_name')
                            ->label(__('blogr-comments::messages.your_name')),
                        TextEntry::make('author_email')
                            ->label(__('blogr-comments::messages.your_email')),
                    ]),
                Section::make(__('blogr-comments::messages.comment_details'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('post_slug')
                            ->label(__('blogr-comments::messages.post')),
                        TextEntry::make('status')
                            ->label(__('blogr-comments::messages.filter_status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                'spam' => 'gray',
                            }),
                        TextEntry::make('vote_score')
                            ->label('Votes'),
                        TextEntry::make('ip_address')
                            ->label(__('blogr-comments::messages.ip_address')),
                        TextEntry::make('created_at')
                            ->label(__('blogr-comments::messages.submitted_on'))
                            ->dateTime(),
                        TextEntry::make('edited_at')
                            ->label(__('blogr-comments::messages.edited'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }

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
