<?php

namespace Happytodev\BlogrComments\Http\Controllers;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Services\CommentService;
use Happytodev\BlogrComments\Services\SpamService;
use Illuminate\Http\JsonResponse;
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

    public function index(string $postSlug, Request $request): View|JsonResponse
    {
        \Log::debug('CommentController::index called', [
            'postSlug' => $postSlug,
            'method' => $request->method(),
        });

        $sort = $request->get('sort', 'newest');
        $comments = $this->commentService->getComments($postSlug, $sort);

        if ($request->expectsJson()) {
            return response()->json([
                'comments' => $comments,
                'total' => $comments->count(),
            ]);
        }

        return view('blogr-comments::comments', compact('comments', 'postSlug', 'sort'));
    }

    public function store(string $postSlug, Request $request): JsonResponse
    {
        \Log::debug('CommentController::store called', [
            'postSlug' => $postSlug,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|min:2|max:5000',
            'captcha_token' => 'nullable|string',
        ]);

        if (config('blogr-comments.spam.captcha.provider') !== 'none') {
            if (empty($validated['captcha_token'])) {
                return response()->json(['error' => __('blogr-comments::messages.captcha_required')], 422);
            }

            if (! $this->spamService->verifyCaptcha($validated['captcha_token'])) {
                return response()->json(['error' => __('blogr-comments::messages.captcha_failed')], 422);
            }
        }

        if ($this->spamService->isSpam($validated['content'], $validated['author_email'], $request->ip())) {
            return response()->json(['comment_status' => 'spam']);
        }

        $this->commentService->createComment([
            'post_slug' => $postSlug,
            'parent_id' => null,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
        ], $request->ip(), $request->userAgent());

        return response()->json(['comment_status' => 'submitted']);
    }

    public function reply(Comment $comment, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|min:2|max:5000',
        ]);

        $depth = $this->getDepth($comment);

        if ($depth >= config('blogr-comments.threading.max_depth', 3)) {
            return response()->json(['error' => __('blogr-comments::messages.max_depth_reached')], 422);
        }

        if ($this->spamService->isSpam($validated['content'], $validated['author_email'], $request->ip())) {
            return response()->json(['comment_status' => 'spam']);
        }

        $this->commentService->createReply($comment, [
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
        ], $request->ip(), $request->userAgent());

        return response()->json(['comment_status' => 'submitted']);
    }

    public function vote(Comment $comment, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        $voteType = $validated['vote'] === 'up' ? 1 : -1;

        $score = $this->commentService->vote($comment, $voteType, $request->ip(), $request->userAgent());

        return response()->json(['vote_score' => $score]);
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
