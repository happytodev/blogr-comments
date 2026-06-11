<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" crossorigin="anonymous">

<div id="comments" x-data="comments()" x-init="init('{{ $postSlug }}')" class="blogr-comments mt-12">
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
        .blogr-comment-content h2 { font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem; color: inherit; }
        .blogr-comment-content code { background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem; }
        .blogr-comment-content pre { background: #1e293b !important; color: #e2e8f0; border-radius: 0.5rem; padding: 1rem; overflow-x: auto; margin-bottom: 0.75rem; position: relative; border: 1px solid #334155; }
        .blogr-comment-content pre code { background: transparent !important; padding: 0; border-radius: 0; font-size: 0.875rem; color: inherit; line-height: 1.5; }
        .blogr-comment-content pre .blogr-code-lang { position: absolute; top: 0.25rem; right: 0.5rem; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; background: #1e293b !important; padding: 0.1rem 0.3rem; border-radius: 0.2rem; }
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
        .blogr-comment-form .btn-submit-row { display: flex; justify-content: space-between; align-items: center; }
        .blogr-comment-char-counter { font-size: 0.75rem; color: #9ca3af; }
        .blogr-comment-char-counter.warning { color: #f59e0b; }
        .blogr-comment-char-counter.danger { color: #ef4444; }
        .blogr-sort-bar { display: flex; gap: 0.5rem; margin-top: 1rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .blogr-sort-btn { background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #6b7280; }
        .blogr-sort-btn.active { background: #e0e7ff; color: #4338ca; font-weight: 500; }
        .dark .blogr-sort-btn.active { background: #312e81; color: #a5b4fc; }
        .blogr-comment-header { display: flex; align-items: center; gap: 0.5rem; }
        .blogr-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; }
        .blogr-comment-form .reply-indicator { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; }
        .blogr-comment-form .reply-indicator button { color: var(--color-primary, #3b82f6); background: none; border: none; cursor: pointer; padding: 0; }
        .blogr-comment-form .reply-indicator button:hover { text-decoration: underline; }
        .blogr-md-toolbar { display: flex; gap: 0.25rem; margin-bottom: 0.25rem; flex-wrap: wrap; }
        .blogr-md-btn { background: none; border: 1px solid #d1d5db; border-radius: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.8125rem; cursor: pointer; color: #374151; line-height: 1.4; }
        .dark .blogr-md-btn { border-color: #4b5563; color: #d1d5db; }
        .blogr-md-btn:hover { background: #f3f4f6; }
        .dark .blogr-md-btn:hover { background: #374151; }
        .blogr-md-btn.active { background: #e0e7ff; color: #4338ca; border-color: #4338ca; }
        .dark .blogr-md-btn.active { background: #312e81; color: #a5b4fc; border-color: #a5b4fc; }
        .blogr-comment-preview { padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 0.5rem; min-height: 6rem; font-size: 0.9375rem; line-height: 1.6; }
        .dark .blogr-comment-preview { border-color: #4b5563; color: #d1d5db; }
        .blogr-comment-preview p { margin-bottom: 0.5rem; }
        .blogr-comment-preview h2 { font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem; color: inherit; }
        .blogr-comment-preview code { background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem; }
        .dark .blogr-comment-preview code { background: #374151; }
        .blogr-comment-preview pre { background: #1e293b !important; color: #e2e8f0; border-radius: 0.5rem; padding: 1rem; overflow-x: auto; margin-bottom: 0.75rem; position: relative; border: 1px solid #334155; }
        .blogr-comment-preview pre code { background: transparent !important; padding: 0; border-radius: 0; font-size: 0.875rem; color: inherit; line-height: 1.5; }
        .blogr-comment-preview pre .blogr-code-lang { position: absolute; top: 0.25rem; right: 0.5rem; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; background: #1e293b !important; padding: 0.1rem 0.3rem; border-radius: 0.2rem; }
        .blogr-comment-status { padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .blogr-comment-status.success { background: #d1fae5; color: #065f46; }
        .blogr-comment-status.error { background: #fee2e2; color: #991b1b; }
        .blogr-permalink-btn { background: none; border: none; cursor: pointer; padding: 0.25rem; font-size: 0.75rem; line-height: 1; color: #9ca3af; margin-left: auto; }
        .blogr-permalink-btn:hover { color: var(--color-primary, #4f46e5); }
        .blogr-comment.highlight { border-color: var(--color-primary, #4f46e5) !important; box-shadow: 0 0 0 2px var(--color-primary, #4f46e5); transition: box-shadow 0.3s; }
        .dark .blogr-comment.highlight { border-color: var(--color-primary, #818cf8) !important; box-shadow: 0 0 0 2px var(--color-primary, #818cf8); }
    </style>

    <h3 class="text-xl font-bold mb-6">{{ __('blogr-comments::messages.comments') }} (<span x-text="totalComments"></span>)</h3>

    <div x-show="statusMessage" x-text="statusMessage" :class="'blogr-comment-status ' + (statusError ? 'error' : 'success')" x-cloak></div>

    <div class="blogr-comment-form">
        <form @submit.prevent="submitComment">
            <input type="text" x-model="form.author_name" placeholder="{{ __('blogr-comments::messages.your_name') }}" required>
            <input type="email" x-model="form.author_email" placeholder="{{ __('blogr-comments::messages.your_email') }}" required>
            <div class="blogr-md-toolbar">
                <button @mousedown.prevent="insertMarkdown('**', '**')" type="button" class="blogr-md-btn" title="{{ __('blogr-comments::messages.bold') }}"><strong>B</strong></button>
                <button @mousedown.prevent="insertMarkdown('*', '*')" type="button" class="blogr-md-btn" title="{{ __('blogr-comments::messages.italic') }}"><em>I</em></button>
                <button @mousedown.prevent="insertMarkdown('`', '`')" type="button" class="blogr-md-btn" title="{{ __('blogr-comments::messages.inline_code') }}">&lt;/&gt;</button>
                <button @mousedown.prevent="insertMarkdown('[', '](url)')" type="button" class="blogr-md-btn" title="{{ __('blogr-comments::messages.link') }}">🔗</button>
                <button @mousedown.prevent="insertHeading" type="button" class="blogr-md-btn" title="Heading 2">H2</button>
                <button @mousedown.prevent="togglePreview" type="button" class="blogr-md-btn" :class="{'active': showPreview}" x-text="showPreview ? '{{ __('blogr-comments::messages.preview') }}' : '{{ __('blogr-comments::messages.preview') }}'"></button>
            </div>
            <textarea x-model="form.content" rows="4" placeholder="{{ __('blogr-comments::messages.write_comment') }}" x-show="!showPreview" required></textarea>
            <div x-show="showPreview" class="blogr-comment-preview" x-html="previewHtml"></div>
            <div x-show="replyTo" class="reply-indicator">
                {{ __('blogr-comments::messages.reply') }} <strong x-text="replyTo ? replyTo.author_name : ''"></strong>
                <button @click="cancelReply" type="button">({{ __('blogr-comments::messages.cancel_reply') }})</button>
            </div>
            <div x-show="error" class="error" x-text="error"></div>
            <div class="btn-submit-row">
                <button type="submit" class="btn-submit">{{ __('blogr-comments::messages.submit') }}</button>
                <span class="blogr-comment-char-counter" :class="{'warning': form.content.length > maxCommentLength * 0.9, 'danger': form.content.length > maxCommentLength}">
                    <span x-text="form.content.length"></span> / <span x-text="maxCommentLength"></span>
                </span>
            </div>
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
                    <img :src="'https://www.gravatar.com/avatar/' + comment.author_email_hash + '?s=32&d=mp'" class="blogr-avatar" alt="">
                    <div>
                        <span class="blogr-comment-author" x-text="comment.author_name"></span>
                        <span class="blogr-comment-time" x-text="timeAgo(comment.created_at)"></span>
                    </div>
                    <button @click="permalink(comment.id)" type="button" class="blogr-permalink-btn" :title="copiedCommentId === comment.id ? '{{ __('blogr-comments::messages.copied') }}' : '{{ __('blogr-comments::messages.copy_link') }}'" x-text="copiedCommentId === comment.id ? '✓' : '🔗'"></button>
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
                                <img :src="'https://www.gravatar.com/avatar/' + reply.author_email_hash + '?s=32&d=mp'" class="blogr-avatar" alt="">
                                <div>
                                    <span class="blogr-comment-author" x-text="reply.author_name"></span>
                                    <span class="blogr-comment-time" x-text="timeAgo(reply.created_at)"></span>
                                </div>
                                <button @click="permalink(reply.id)" type="button" class="blogr-permalink-btn" :title="copiedCommentId === reply.id ? '{{ __('blogr-comments::messages.copied') }}' : '{{ __('blogr-comments::messages.copy_link') }}'" x-text="copiedCommentId === reply.id ? '✓' : '🔗'"></button>
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
        showPreview: false,
        previewHtml: '',
        copiedCommentId: null,
        maxCommentLength: {{ config('blogr-comments.editing.max_comment_length', 5000) }},
        navEnabled: {{ config('blogr.ui.navigation.enabled', true) ? 'true' : 'false' }},
        navSticky: {{ config('blogr.ui.navigation.sticky', true) ? 'true' : 'false' }},
        form: { author_name: '', author_email: '', content: '' },

        init(slug) {
            if (! slug) return;
            this.postSlug = slug;
            this.loadComments();
        },

        computeScrollOffset() {
            const offset = (this.navEnabled && this.navSticky) ? 96 : 16;
            const isMobile = window.innerWidth < 768;
            return isMobile ? offset + 80 : offset;
        },

        permalink(commentId) {
            const url = window.location.origin + window.location.pathname + '#comment-' + commentId;
            navigator.clipboard.writeText(url).then(() => {
                this.copiedCommentId = commentId;
                setTimeout(() => { this.copiedCommentId = null; }, 2000);
            }).catch(() => {
                window.location.hash = 'comment-' + commentId;
            });
        },

        scrollToComment() {
            const hash = window.location.hash;
            if (! hash.startsWith('#comment-') && hash !== '#comments') return;
            this.$nextTick(() => {
                const el = document.getElementById(hash.substring(1));
                if (! el) return;
                const top = el.getBoundingClientRect().top + window.pageYOffset - this.computeScrollOffset();
                window.scrollTo({ top, behavior: 'smooth' });
                if (hash.startsWith('#comment-')) {
                    el.classList.add('highlight');
                    setTimeout(() => el.classList.remove('highlight'), 3000);
                }
            });
        },

        loadComments() {
            if (! this.postSlug) return;
            fetch('/comments/' + this.postSlug + '?sort=' + this.sort + '&_=' + Date.now(), {
                headers: { 'Accept': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    this.comments = data.comments || [];
                    this.totalComments = data.total || this.comments.length;
                    this.scrollToComment();
                })
                .catch(err => console.warn('blogr-comments: failed to load', err));
        },

        submitComment() {
            this.error = '';
            this.statusMessage = '';

            const csrfMeta = document.querySelector('meta[name=csrf-token]');

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
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                }
            })
                .then(r => {
                    return r.text().then(text => {
                        let json;
                        try { json = JSON.parse(text); } catch(e) { json = { raw: text }; }
                        return { status: r.status, body: json, raw: text };
                    });
                })
                .then(({ status, body }) => {
                    if (body.comment_status === 'approved') {
                        this.form = { author_name: '', author_email: '', content: '' };
                        this.replyTo = null;
                        this.statusMessage = '{{ __('blogr-comments::messages.comment_approved') }}';
                        this.statusError = false;
                        this.loadComments();
                    } else if (body.comment_status === 'pending') {
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
                    } else if (body.errors) {
                        const contentErr = body.errors.content?.[0];
                        const firstErr = Object.values(body.errors).flat().find(Boolean);
                        this.error = contentErr || firstErr || '{{ __('blogr-comments::messages.an_error_occurred') }}';
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
            this.statusMessage = '';
            this.statusError = false;

            fetch('/comments/' + commentId + '/vote', {
                method: 'POST',
                body: new URLSearchParams({ vote: type }),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                }
            })
                .then(r => {
                    if (! r.ok) {
                        if (r.status === 429) {
                            return r.json().then(data => {
                                this.statusMessage = data.error || '{{ __('blogr-comments::messages.rate_limit_exceeded', ['minutes' => 1]) }}';
                                this.statusError = true;
                            });
                        }
                        throw r;
                    }
                    return r.json().then(data => {
                        if (data.vote_score !== undefined) {
                            const comment = this.comments.find(c => c.id === commentId);
                            if (comment) comment.vote_score = data.vote_score;
                        }
                    });
                })
                .catch(() => {});
        },

        insertMarkdown(before, after) {
            const textarea = document.activeElement?.tagName === 'TEXTAREA' ? document.activeElement : this.$el.querySelector('textarea');
            if (! textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = this.form.content;
            const selected = text.substring(start, end);
            const replacement = before + selected + after;
            this.form.content = text.substring(0, start) + replacement + text.substring(end);
            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + before.length, start + before.length + selected.length);
            });
        },

        insertHeading() {
            const textarea = document.activeElement?.tagName === 'TEXTAREA' ? document.activeElement : this.$el.querySelector('textarea');
            if (! textarea) return;
            const cursor = textarea.selectionStart;
            const text = this.form.content;
            let lineStart = cursor;
            while (lineStart > 0 && text[lineStart - 1] !== '\n') lineStart--;
            let lineEnd = cursor;
            while (lineEnd < text.length && text[lineEnd] !== '\n') lineEnd++;
            const line = text.substring(lineStart, lineEnd);
            const indent = line.match(/^\s*/)[0];
            const newLine = indent + '## ' + line.trim();
            this.form.content = text.substring(0, lineStart) + newLine + text.substring(lineEnd);
            this.$nextTick(() => {
                textarea.focus();
                const pos = lineStart + indent.length + 3;
                textarea.setSelectionRange(pos, pos + line.trim().length);
            });
        },

        togglePreview() {
            this.showPreview = ! this.showPreview;
            if (this.showPreview && this.form.content) {
                fetch('/comments/preview', {
                    method: 'POST',
                    body: new URLSearchParams({ content: this.form.content }),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                })
                    .then(r => r.json())
                .then(data => {
                    this.previewHtml = data.html || '';
                })
                .catch(() => {
                    this.previewHtml = '<p>Preview unavailable</p>';
                });
            } else {
                this.previewHtml = '';
            }
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
        }
    };
}
</script>
