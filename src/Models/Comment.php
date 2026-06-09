<?php

namespace Happytodev\BlogrComments\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'blogr_comments';

    protected $fillable = [
        'post_slug',
        'parent_id',
        'author_name',
        'author_email',
        'content',
        'content_html',
        'status',
        'ip_address',
        'user_agent',
        'vote_score',
        'edited_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'vote_score' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class);
    }

    public function subscription()
    {
        return $this->hasOne(CommentSubscription::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
