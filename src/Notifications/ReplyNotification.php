<?php

namespace Happytodev\BlogrComments\Notifications;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Models\CommentSubscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyNotification extends Notification
{
    public function __construct(
        protected Comment $reply,
        protected ?CommentSubscription $subscription = null,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $postLink = url('/') . '/' . $this->reply->post_slug;
        $replyLink = $postLink . '#comment-' . $this->reply->id;

        $mail = (new MailMessage)
            ->subject(__('blogr-comments::messages.reply_subject', [
                'author' => $this->reply->author_name,
                'post' => $this->reply->post_slug,
            ]))
            ->line(__('blogr-comments::messages.reply_body', [
                'author' => $this->reply->author_name,
                'post' => $this->reply->post_slug,
            ]))
            ->line($this->reply->content)
            ->action(__('blogr-comments::messages.reply_cta'), $replyLink);

        if ($this->subscription && $this->subscription->token) {
            $unsubscribeUrl = route('comments.unsubscribe', ['token' => $this->subscription->token]);
            $mail->line('[<a href="' . $unsubscribeUrl . '">' . __('blogr-comments::messages.unsubscribe') . '</a>]');
        }

        return $mail;
    }
}
