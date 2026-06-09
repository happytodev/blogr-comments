<?php

namespace Happytodev\BlogrComments\Services;

use Happytodev\BlogrComments\Models\Comment;

class ModerationService
{
    public function determineStatus(string $authorEmail, string $ip): string
    {
        $mode = config('blogr-comments.moderation.mode', 'post');

        if ($mode === 'pre') {
            return 'pending';
        }

        if ($mode === 'trust') {
            $threshold = config('blogr-comments.moderation.trust_threshold', 3);
            $approvedCount = Comment::where('author_email', $authorEmail)
                ->where('status', 'approved')
                ->count();

            if ($approvedCount >= $threshold) {
                return 'approved';
            }

            return 'pending';
        }

        return 'approved';
    }
}
