<?php

namespace Happytodev\BlogrComments\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentDigestNotification extends Notification
{
    public function __construct(
        protected array $comments,
        protected string $period,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->period === 'daily'
            ? __('blogr-comments::messages.digest_daily_subject')
            : __('blogr-comments::messages.digest_weekly_subject');

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting($title);

        $grouped = $this->comments->groupBy('post_slug');

        foreach ($grouped as $postSlug => $postComments) {
            $first = $postComments->first();
            $postTitle = $first->post_title ?? $postSlug;

            $mail->line('---');
            $mail->line('**' . $postTitle . '** — ' . $postComments->count() . ' ' . __('blogr-comments::messages.digest_comment_count', ['count' => $postComments->count()]));

            foreach ($postComments->take(5) as $comment) {
                $mail->line('• ' . $comment->author_name . ': "' . mb_substr($comment->content, 0, 100) . '"');
            }

            if ($postComments->count() > 5) {
                $mail->line(__('blogr-comments::messages.digest_more_comments', ['count' => $postComments->count() - 5]));
            }

            $mail->action(__('blogr-comments::messages.digest_view_post'), url('/' . $postSlug));
        }

        $mail->line(__('blogr-comments::messages.digest_footer'));

        return $mail;
    }
}
