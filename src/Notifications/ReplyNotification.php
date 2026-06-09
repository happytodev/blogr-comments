<?php

namespace Happytodev\BlogrComments\Notifications;

use Happytodev\BlogrComments\Models\Comment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyNotification extends Notification
{
    public function __construct(protected Comment $reply) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $postLink = url('/') . '/' . $this->reply->post_slug;
        $replyLink = $postLink . '#comment-' . $this->reply->id;

        return (new MailMessage)
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
    }
}
