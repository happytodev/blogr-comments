<?php

namespace Happytodev\BlogrComments\Notifications;

use Happytodev\BlogrComments\Models\Comment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    public function __construct(protected Comment $comment) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $postLink = url('/') . '/' . $this->comment->post_slug;
        $commentLink = $postLink . '#comment-' . $this->comment->id;

        return (new MailMessage)
            ->subject(__('blogr-comments::messages.new_comment_subject', ['post' => $this->comment->post_slug]))
            ->line(__('blogr-comments::messages.new_comment_body', [
                'author' => $this->comment->author_name,
                'post' => $this->comment->post_slug,
            ]))
            ->line($this->comment->content)
            ->action(__('blogr-comments::messages.new_comment_cta'), $commentLink);
    }
}
