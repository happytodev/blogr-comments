<?php

namespace Happytodev\BlogrComments\Filament\Resources\CommentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;
use Filament\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Happytodev\BlogrComments\Filament\Resources\CommentResource;
use Happytodev\BlogrComments\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')
                    ->label(__('blogr-comments::messages.author_info'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('content')
                    ->label(__('blogr-comments::messages.comment'))
                    ->limit(80)
                    ->html(),
                TextColumn::make('post_slug')
                    ->label(__('blogr-comments::messages.post'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('blogr-comments::messages.filter_status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icon('heroicon-o-shield-exclamation')
                    ->sortable(),
                TextColumn::make('vote_score')
                    ->label('Votes')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('blogr-comments::messages.submitted_on'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => __('blogr-comments::messages.pending'),
                        'approved' => __('blogr-comments::messages.approved'),
                        'rejected' => __('blogr-comments::messages.rejected'),
                        'spam' => __('blogr-comments::messages.spam'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                BulkAction::make('approve')
                    ->label(__('blogr-comments::messages.bulk_approve'))
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'approved']))
                    ->color('success'),
                BulkAction::make('reject')
                    ->label(__('blogr-comments::messages.bulk_reject'))
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected']))
                    ->color('danger'),
                BulkAction::make('spam')
                    ->label(__('blogr-comments::messages.bulk_spam'))
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'spam']))
                    ->color('gray'),
                BulkAction::make('delete')
                    ->label(__('blogr-comments::messages.bulk_delete'))
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->color('danger')
                    ->requiresConfirmation(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
