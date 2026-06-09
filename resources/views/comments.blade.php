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
        .blogr-vote-btn:hover { color: var(--color-primary, #4f46e5); }
        .blogr-vote-btn.voted { color: var(--color-primary, #4f46e5); }
        .blogr-vote-score { font-weight: 600; font-size: 0.875rem; min-width: 1.5rem; text-align: center; color: #374151; }
        .dark .blogr-vote-score { color: #d1d5db; }
        .blogr-comment-form input, .blogr-comment-form textarea {
            width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 0.5rem; font-size: 0.9375rem; box-sizing: border-box;
        }
        .dark .blogr-comment-form input, .dark .blogr-comment-form textarea { background: #374151; border-color: #4b5563; color: #e5e7eb; }
        .blogr-comment-form .error { color: #dc2626; font-size: 0.875rem; margin-bottom: 0.5rem; }
        .blogr-comment-form .btn-submit {
            display: inline-block; padding: 0.625rem 1.25rem; background: var(--color-primary, #3b82f6); color: #fff;
            border: none; border-radius: 0.5rem; font-size: 0.9375rem; font-weight: 500; cursor: pointer;
        }
        .blogr-comment-form .btn-submit:hover { opacity: 0.9; }
        .blogr-sort-bar { display: flex; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .blogr-sort-btn { background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #6b7280; }
        .blogr-sort-btn.active { background: #e0e7ff; color: #4338ca; font-weight: 500; }
        .dark .blogr-sort-btn.active { background: #312e81; color: #a5b4fc; }
        .blogr-comment-header { display: flex; align-items: center; gap: 0.5rem; }
        .blogr-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; }
        .blogr-comment-form .reply-indicator { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; }
        .blogr-comment-form .reply-indicator button { color: var(--color-primary, #3b82f6); background: none; border: none; cursor: pointer; padding: 0; }
        .blogr-comment-form .reply-indicator button:hover { text-decoration: underline; }
        .blogr-comment-status { padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .blogr-comment-status.success { background: #d1fae5; color: #065f46; }
        .blogr-comment-status.error { background: #fee2e2; color: #991b1b; }
    </style>

    <h3 class="text-xl font-bold mb-6">{{ __('blogr-comments::messages.comments') }} (<span x-text="totalComments"></span>)</h3>

    <div x-show="statusMessage" x-text="statusMessage" :class="'blogr-comment-status ' + (statusError ? 'error' : 'success')" x-cloak></div>

    <div class="blogr-comment-form">
        <form @submit.prevent="submitComment">
            <input type="text" x-model="form.author_name" placeholder="{{ __('blogr-comments::messages.your_name') }}" required>
            <input type="email" x-model="form.author_email" placeholder="{{ __('blogr-comments::messages.your_email') }}" required>
            <textarea x-model="form.content" rows="4" placeholder="{{ __('blogr-comments::messages.write_comment') }}" required></textarea>
            <div x-show="replyTo" class="reply-indicator">
                {{ __('blogr-comments::messages.reply') }} <strong x-text="replyTo ? replyTo.author_name : ''"></strong>
                <button @click="cancelReply" type="button">({{ __('blogr-comments::messages.cancel_reply') }})</button>
            </div>
            <div x-show="error" class="error" x-text="error"></div>
            <button type="submit" class="btn-submit">{{ __('blogr-comments::messages.submit') }}</button>
        </form>
    </div>

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
                        <button @click="vote(comment.id, 'up')" :class="{'voted': comment.user_vote === 1}" class="blogr-vote-btn">&#9650;</button>
                        <span class="blogr-vote-score" x-text="comment.vote_score"></span>
                        <button @click="vote(comment.id, 'down')" :class="{'voted': comment.user_vote === -1}" class="blogr-vote-btn">&#9660;</button>
                    </div>
                    <button @click="setReply(comment)" class="text-sm hover:underline" style="color: var(--color-primary, #3b82f6); background: none; border: none; cursor: pointer;">
                        {{ __('blogr-comments::messages.reply') }}
                    </button>
                </div>
            </div>
            <template x-if="comment.replies && comment.replies.length > 0">
                <div class="blogr-comment-thread">
                    <template x-for="reply in comment.replies" :key="reply.id">
                        <div class="blogr-comment" :id="'comment-' + reply.id">
                            <div class="blogr-comment-header">
                                <img :src="'https://www.gravatar.com/avatar/' + md5(reply.author_email) + '?s=32&d=mp'" class="blogr-avatar" alt="">
                                <div>
                                    <span class="blogr-comment-author" x-text="reply.author_name"></span>
                                    <span class="blogr-comment-time" x-text="timeAgo(reply.created_at)"></span>
                                </div>
                            </div>
                            <div class="blogr-comment-content" x-html="reply.content_html"></div>
                        </div>
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
        error: '',
        statusMessage: '',
        statusError: false,
        form: { author_name: '', author_email: '', content: '' },

        init(slug) {
            this.postSlug = slug;
            this.loadComments();
        },

        loadComments() {
            fetch('/comments/' + this.postSlug + '?sort=' + this.sort, {
                headers: { 'Accept': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    this.comments = data.comments || [];
                    this.totalComments = data.total || this.comments.length;
                })
                .catch(() => {});
        },

        submitComment() {
            this.error = '';
            this.statusMessage = '';

            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            console.log('🔑 CSRF token found:', !!csrfMeta, csrfMeta?.content?.substring(0, 10) + '...');

            let url = '/comments/' + this.postSlug;
            let body = new FormData();
            body.append('author_name', this.form.author_name);
            body.append('author_email', this.form.author_email);
            body.append('content', this.form.content);

            if (this.replyTo) {
                url = '/comments/' + this.replyTo.id + '/reply';
            }

            fetch(url, {
                method: 'POST',
                body,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
            })
                .then(r => {
                    console.log('📬 Comment POST to:', url);
                    console.log('📊 Response status:', r.status);
                    return r.text().then(text => {
                        console.log('📄 Response text:', text.substring(0, 500));
                        let json;
                        try { json = JSON.parse(text); } catch(e) { json = { raw: text }; }
                        return { status: r.status, body: json, raw: text };
                    });
                })
                .then(({ status, body }) => {
                    if (body.comment_status === 'submitted') {
                        this.form = { author_name: '', author_email: '', content: '' };
                        this.replyTo = null;
                        this.statusMessage = '{{ __('blogr-comments::messages.comment_pending') }}';
                        this.statusError = false;
                        this.loadComments();
                    } else if (body.comment_status === 'spam') {
                        this.statusMessage = '{{ __('blogr-comments::messages.comment_spam') }}';
                        this.statusError = true;
                    } else if (body.error) {
                        console.error('❌ Server error:', body.error);
                        this.error = body.error;
                    } else {
                        console.error('❌ Unexpected response:', status, body);
                        this.error = '{{ __('blogr-comments::messages.an_error_occurred') }} (status: ' + status + ')';
                    }
                })
                .catch(err => {
                    console.error('❌ Network/fetch error:', err);
                    this.error = '{{ __('blogr-comments::messages.an_error_occurred') }}';
                });
        },

        vote(commentId, type) {
            fetch('/comments/' + commentId + '/vote', {
                method: 'POST',
                body: new URLSearchParams({ vote: type }),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.vote_score !== undefined) {
                        const comment = this.comments.find(c => c.id === commentId);
                        if (comment) comment.vote_score = data.vote_score;
                    }
                })
                .catch(() => {});
        },

        setReply(comment) {
            this.replyTo = comment;
            this.form.content = '';
        },

        cancelReply() {
            this.replyTo = null;
        },

        timeAgo(dateStr) {
            const d = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return '{{ __('blogr-comments::messages.just_now') }}';
            if (diff < 3600) return Math.floor(diff / 60) + 'm';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h';
            return d.toLocaleDateString();
        },

        md5(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                const chr = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash |= 0;
            }
            return Math.abs(hash).toString(16).padStart(32, '0');
        }
    };
}
</script>
