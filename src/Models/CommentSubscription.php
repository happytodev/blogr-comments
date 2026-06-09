<?php

namespace Happytodev\BlogrComments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommentSubscription extends Model
{
    protected $table = 'blogr_comment_subscriptions';

    protected $fillable = [
        'comment_id',
        'email',
        'token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            $subscription->token = $subscription->token ?: Str::random(32);
        });
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
