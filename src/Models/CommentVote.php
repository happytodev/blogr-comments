<?php

namespace Happytodev\BlogrComments\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    protected $table = 'blogr_comment_votes';

    protected $fillable = [
        'comment_id',
        'vote_type',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'vote_type' => 'integer',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
