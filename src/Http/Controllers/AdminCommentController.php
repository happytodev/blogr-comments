<?php

namespace Happytodev\BlogrComments\Http\Controllers;

use Happytodev\BlogrComments\Filament\Resources\CommentResource;
use Happytodev\BlogrComments\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminCommentController extends Controller
{
    public function moderate(Request $request, Comment $comment, string $action)
    {
        $status = match ($action) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'spam' => 'spam',
            default => abort(404),
        };

        $comment->update(['status' => $status]);

        session()->flash('success', __(
            'blogr-comments::messages.admin_comment_' . ($action === 'spam' ? 'marked_spam' : $action),
        ));

        return redirect(CommentResource::getUrl('index'));
    }
}
