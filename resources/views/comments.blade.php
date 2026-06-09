<div x-data="comments()" x-init="init('{{ $postSlug }}')" class="blogr-comments mt-12">
    <style>
        .blogr-comments { max-width: 100%; }
        .blogr-comment { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; background: #fff; }
        .dark .blogr-comment { border-color: #374151; background: #1f2937; }
        .blogr-comment-thread { margin-left: 1.5rem; padding-left: 1rem; border-left: 2px solid #e5e7eb; }
        .dark .blogr-comment-thread { border-left-color: #374151; }
        .blogr-comment-author { font-weight: 600; color: #111827; }
        .dark .blogr-comment-author { color: #e5e7eb; }
        .blogr-comment-time { font-size: 0.75rem; color: #6b7280; }
        .blogr-comment-content { margin-top: 0.5rem; line-height: 1.6; color: #374151; }
        .dark .blogr-comment-content { color: #d1d5db; }
        .blogr-comment-content p { margin-bottom: 0.5rem; }
        .blogr-comment-content code { background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem; }
        .blogr-comment-actions { margin-top: 0.5rem; display: flex; gap: 0.75rem; align-items: center; font-size: 0.875rem; }
        .blogr-comment-votes { display: flex; align-items: center; gap: 0.25rem; }
        .blogr-vote-btn { background: none; border: none; cursor: pointer; padding: 0.25rem; color: #9ca3af; line-height: 1; }
        .blogr-vote-btn:hover { color: #4f46e5; }
        .blogr-vote-btn.voted { color: #4f46e5; }
        .blogr-vote-score { font-weight: 600; font-size: 0.875rem; min-width: 1.5rem; text-align: center; color: #374151; }
        .dark .blogr-vote-score { color: #d1d5db; }
        .blogr-comment-form { margin-bottom: 1.5rem; }
        .blogr-comment-form input, .blogr-comment-form textarea { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 0.5rem; }
        .dark .blogr-comment-form input, .dark .blogr-comment-form textarea { background: #374151; border-color: #4b5563; color: #e5e7eb; }
        .blogr-comment-form .error { color: #dc2626; font-size: 0.875rem; }
        .blogr-sort-bar { display: flex; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .blogr-sort-btn { background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #6b7280; }
        .blogr-sort-btn.active { background: #e0e7ff; color: #4338ca; font-weight: 500; }
        .dark .blogr-sort-btn.active { background: #312e81; color: #a5b4fc; }
        .blogr-load-more { text-align: center; margin-top: 1rem; }
        .blogr-avatar { width: 32px; height: 32px; border-radius: 50%; background: #e5e7eb; flex-shrink: 0; }
        .blogr-comment-header { display: flex; align-items: center; gap: 0.5rem; }
    </style>

    <h3 class="text-xl font-bold mb-6">{{ __('blogr-comments::messages.comments') }} (<span x-text="totalComments"></span>)</h3>

    <template x-if="!threadClosed">
        <div class="blogr-comment-form">
            <form @submit.prevent="submitComment">
                <input type="text" x-model="form.author_name" placeholder="{{ __('blogr-comments::messages.your_name') }}" required>
                <input type="email" x-model="form.author_email" placeholder="{{ __('blogr-comments::messages.your_email') }}" required>
                <textarea x-model="form.content" rows="3" placeholder="{{ __('blogr-comments::messages.write_comment') }}" required></textarea>
                <template x-if="replyTo">
                    <div class="text-sm text-gray-500 mb-2">
                        {{ __('blogr-comments::messages.reply') }}
                        <strong x-text="replyTo.author_name"></strong>
                        <button @click="cancelReply" class="text-primary-600 ml-2">{{ __('blogr-comments::messages.cancel_reply') }}</button>
                    </div>
                </template>
                <div x-show="error" class="error" x-text="error"></div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500">
                    {{ __('blogr-comments::messages.submit') }}
                </button>
            </form>
        </div>
    </template>

    <div class="blogr-sort-bar">
        <button @click="sort = 'newest'; loadComments()" :class="{'active': sort === 'newest'}" class="blogr-sort-btn">{{ __('blogr-comments::messages.sort_newest') }}</button>
        <button @click="sort = 'oldest'; loadComments()" :class="{'active': sort === 'oldest'}" class="blogr-sort-btn">{{ __('blogr-comments::messages.sort_oldest') }}</button>
        <button @click="sort = 'best'; loadComments()" :class="{'active': sort === 'best'}" class="blogr-sort-btn">{{ __('blogr-comments::messages.sort_best') }}</button>
    </div>

    <template x-if="comments.length === 0">
        <p class="text-gray-500 text-center py-8">{{ __('blogr-comments::messages.no_comments') }}</p>
    </template>

    <template x-for="comment in comments" :key="comment.id">
        <div>
            <div class="blogr-comment" :id="'comment-' + comment.id">
                <div class="blogr-comment-header">
                    <img :src="'https://www.gravatar.com/avatar/' + md5(comment.author_email) + '?s=32&d=mp'" class="blogr-avatar" alt="">
                    <div>
                        <span class="blogr-comment-author" x-text="comment.author_name"></span>
                        <span class="blogr-comment-time" x-text="timeAgo(comment.created_at)"></span>
                    </div>
                </div>
                <div class="blogr-comment-content" x-html="comment.content_html"></div>
                <div class="blogr-comment-actions">
                    <div class="blogr-comment-votes">
                        <button @click="vote(comment.id, 'up')" :class="{'voted': comment.user_vote === 1}" class="blogr-vote-btn">▲</button>
                        <span class="blogr-vote-score" x-text="comment.vote_score"></span>
                        <button @click="vote(comment.id, 'down')" :class="{'voted': comment.user_vote === -1}" class="blogr-vote-btn">▼</button>
                    </div>
                    <button @click="setReply(comment)" class="text-sm text-primary-600 hover:text-primary-500">
                        {{ __('blogr-comments::messages.reply') }}
                    </button>
                </div>
            </div>
            <template x-if="comment.replies && comment.replies.length > 0">
                <div class="blogr-comment-thread">
                    <template x-for="reply in comment.replies" :key="reply.id">
                        <template x-if="reply.status === 'approved'">
                            <div class="blogr-comment" :id="'comment-' + reply.id">
                                <div class="blogr-comment-header">
                                    <img :src="'https://www.gravatar.com/avatar/' + md5(reply.author_email) + '?s=32&d=mp'" class="blogr-avatar" alt="">
                                    <div>
                                        <span class="blogr-comment-author" x-text="reply.author_name"></span>
                                        <span class="blogr-comment-time" x-text="timeAgo(reply.created_at)"></span>
                                    </div>
                                </div>
                                <div class="blogr-comment-content" x-html="reply.content_html"></div>
                                <div class="blogr-comment-actions">
                                    <button @click="setReply(comment)" class="text-sm text-primary-600 hover:text-primary-500">
                                        {{ __('blogr-comments::messages.reply') }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </template>
        </div>
    </template>
</div>

<script>
function comments() {
    return {
        comments: [],
        totalComments: 0,
        sort: 'newest',
        postSlug: '',
        replyTo: null,
        threadClosed: false,
        error: '',
        form: { author_name: '', author_email: '', content: '' },

        init(slug) {
            this.postSlug = slug;
            this.loadComments();
        },

        loadComments() {
            fetch('/comments/' + this.postSlug + '?sort=' + this.sort)
                .then(r => r.text())
                .then(html => {
                    // Parse HTML response to extract comments
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    // The server renders the partial, we'll use JSON for now
                });
        },

        submitComment() {
            let url = '/comments/' + this.postSlug;
            let body = new FormData();
            body.append('author_name', this.form.author_name);
            body.append('author_email', this.form.author_email);
            body.append('content', this.form.content);

            if (this.replyTo) {
                url = '/comments/' + this.replyTo.id + '/reply';
            }

            fetch(url, { method: 'POST', body, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } })
                .then(r => r.json().catch(() => r.text()))
                .then(data => {
                    if (data.status === 'submitted' || data.comment_status === 'submitted') {
                        this.form = { author_name: '', author_email: '', content: '' };
                        this.replyTo = null;
                        this.loadComments();
                    } else {
                        this.error = 'An error occurred.';
                    }
                })
                .catch(() => { this.error = 'An error occurred.'; });
        },

        vote(commentId, type) {
            fetch('/comments/' + commentId + '/vote', {
                method: 'POST',
                body: new URLSearchParams({ vote: type }),
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(() => this.loadComments());
        },

        setReply(comment) {
            this.replyTo = comment;
        },

        cancelReply() {
            this.replyTo = null;
        },

        timeAgo(date) {
            const d = new Date(date);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return d.toLocaleDateString();
        },

        md5(str) {
            // Simple md5 for gravatar - in production use a proper hash
            return str.split('').reduce((hash, c) => (hash << 5) - hash + c.charCodeAt(0), 0).toString(16);
        }
    };
}
</script>
