<?php

namespace Happytodev\BlogrComments\Http\Controllers;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Services\CommentService;
use Happytodev\BlogrComments\Services\SpamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
        protected SpamService $spamService,
    ) {}

    public function index(string $postSlug, Request $request): View
    {
        $sort = $request->get('sort', 'newest');
        $comments = $this->commentService->getComments($postSlug, $sort);

        return view('blogr-comments::comments', compact('comments', 'postSlug', 'sort'));
    }

    public function store(string $postSlug, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|min:2|max:5000',
            'captcha_token' => 'nullable|string',
        ]);

        if (config('blogr-comments.spam.captcha.provider') !== 'none') {
            if (empty($validated['captcha_token'])) {
                return back()->withErrors(['captcha' => __('blogr-comments::messages.captcha_required')]);
            }

            if (! $this->spamService->verifyCaptcha($validated['captcha_token'])) {
                return back()->withErrors(['captcha' => __('blogr-comments::messages.captcha_failed')]);
            }
        }

        if ($this->spamService->isSpam($validated['content'], $validated['author_email'], $request->ip())) {
            return back()->with('comment_status', 'spam');
        }

        $this->commentService->createComment([
            'post_slug' => $postSlug,
            'parent_id' => null,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
        ], $request->ip(), $request->userAgent());

        return back()->with('comment_status', 'submitted');
    }

    public function reply(Comment $comment, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|min:2|max:5000',
        ]);

        $depth = $this->getDepth($comment);

        if ($depth >= config('blogr-comments.threading.max_depth', 3)) {
            return back()->withErrors(['content' => __('blogr-comments::messages.max_depth_reached')]);
        }

        if ($this->spamService->isSpam($validated['content'], $validated['author_email'], $request->ip())) {
            return back()->with('comment_status', 'spam');
        }

        $this->commentService->createReply($comment, [
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
        ], $request->ip(), $request->userAgent());

        return back()->with('comment_status', 'submitted');
    }

    public function vote(Comment $comment, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        $voteType = $validated['vote'] === 'up' ? 1 : -1;

        $this->commentService->vote($comment, $voteType, $request->ip(), $request->userAgent());

        return back();
    }

    protected function getDepth(Comment $comment, int $depth = 0): int
    {
        if ($depth > 10) {
            return $depth;
        }

        if ($comment->parent_id === null) {
            return $depth;
        }

        return $this->getDepth($comment->parent, $depth + 1);
    }
}
