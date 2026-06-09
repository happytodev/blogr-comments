<?php

namespace Happytodev\BlogrComments\Services;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Models\CommentSubscription;
use Happytodev\BlogrComments\Models\CommentVote;
use Happytodev\BlogrComments\Notifications\NewCommentNotification;
use Happytodev\BlogrComments\Notifications\ReplyNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CommentService
{
    public function __construct(
        protected ModerationService $moderation,
        protected SpamService $spam,
        protected CommentRenderer $renderer,
    ) {}

    public function getComments(string $postSlug, string $sort = 'newest'): Collection
    {
        $query = Comment::with(['replies' => function ($q) {
            $q->approved()->orderBy('created_at', 'asc');
        }])->root()->approved()->where('post_slug', $postSlug);

        $query->when($sort === 'oldest', fn ($q) => $q->orderBy('created_at', 'asc'))
              ->when($sort === 'newest', fn ($q) => $q->orderBy('created_at', 'desc'))
              ->when($sort === 'best', fn ($q) => $q->orderBy('vote_score', 'desc'));

        return $query->get();
    }

    public function createComment(array $data, string $ip, ?string $userAgent): Comment
    {
        $contentHtml = $this->renderer->toHtml($data['content']);
        $status = $this->moderation->determineStatus($data['author_email'], $ip);

        $comment = Comment::create([
            'post_slug' => $data['post_slug'],
            'parent_id' => $data['parent_id'] ?? null,
            'author_name' => $data['author_name'],
            'author_email' => $data['author_email'],
            'content' => $data['content'],
            'content_html' => $contentHtml,
            'status' => $status,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        $this->subscribeToParent($comment);

        $this->dispatchNotifications($comment);

        return $comment;
    }

    public function createReply(Comment $parent, array $data, string $ip, ?string $userAgent): Comment
    {
        $data['post_slug'] = $parent->post_slug;
        $data['parent_id'] = $parent->id;

        return $this->createComment($data, $ip, $userAgent);
    }

    public function vote(Comment $comment, int $voteType, string $ip, ?string $userAgent): int
    {
        $existing = CommentVote::where('comment_id', $comment->id)
            ->where('ip_address', $ip)
            ->where('user_agent', $userAgent)
            ->first();

        if ($existing) {
            if ($existing->vote_type === $voteType) {
                return $comment->vote_score;
            }

            $comment->decrement('vote_score', $existing->vote_type);
            $existing->update(['vote_type' => $voteType]);
            $comment->increment('vote_score', $voteType);

            return $comment->fresh()->vote_score;
        }

        CommentVote::create([
            'comment_id' => $comment->id,
            'vote_type' => $voteType,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        $comment->increment('vote_score', $voteType);

        return $comment->fresh()->vote_score;
    }

    protected function subscribeToParent(Comment $comment): void
    {
        if ($comment->parent_id) {
            $parent = $comment->parent;

            $alreadySubscribed = CommentSubscription::where('comment_id', $parent->id)
                ->where('email', $comment->author_email)
                ->exists();

            if (! $alreadySubscribed && $parent->author_email !== $comment->author_email) {
                CommentSubscription::create([
                    'comment_id' => $parent->id,
                    'email' => $comment->author_email,
                ]);
            }
        }
    }

    protected function dispatchNotifications(Comment $comment): void
    {
        if (config('blogr-comments.notifications.new_comment', true)) {
            $ownerEmail = config('blogr-comments.notifications.owner_email')
                ?: config('mail.from.address');

            if ($ownerEmail) {
                Notification::route('mail', $ownerEmail)
                    ->notify(new NewCommentNotification($comment));
            }
        }

        if ($comment->parent_id && config('blogr-comments.notifications.reply', true)) {
            $parent = $comment->parent;
            $subscriptions = CommentSubscription::where('comment_id', $parent->id)->get();

            foreach ($subscriptions as $subscription) {
                if ($subscription->email !== $comment->author_email) {
                    Notification::route('mail', $subscription->email)
                        ->notify(new ReplyNotification($comment));
                }
            }
        }
    }
}
