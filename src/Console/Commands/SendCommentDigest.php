<?php

namespace Happytodev\BlogrComments\Console\Commands;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Notifications\CommentDigestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendCommentDigest extends Command
{
    protected $signature = 'blogr-comments:send-digest {--period=daily}';
    protected $description = 'Send comment digest (daily or weekly) to the admin';

    public function handle(): int
    {
        $period = $this->option('period');

        $since = match ($period) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            default => now()->subDay(),
        };

        $comments = Comment::where('status', 'approved')
            ->where('created_at', '>=', $since)
            ->get();

        if ($comments->isEmpty()) {
            $this->info('No new comments to send.');

            return self::SUCCESS;
        }

        $ownerEmail = config('blogr-comments.notifications.owner_email')
            ?: config('mail.from.address');

        if (! $ownerEmail) {
            $this->error('No owner email configured.');

            return self::FAILURE;
        }

        Notification::route('mail', $ownerEmail)
            ->notify(new CommentDigestNotification($comments, $period));

        $this->info('Digest sent to ' . $ownerEmail);

        return self::SUCCESS;
    }
}
