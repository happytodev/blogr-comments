# Changelog

All notable changes to `blogr-comments` will be documented in this file.

## v1.1.0 - 2026-06-11

### ✨ Features

- **Syntax highlighting**: Server-side code highlighting via `scrivo/highlight.php` with language badge and dark mode support
- **Per-comment permalink**: 🔗 button copies sharable URL with `#comment-{id}` anchor
- **Smart anchor scrolling**: Dynamic scroll offset for fixed/sticky navigation + mobile viewport
- **Character counter**: Real-time countdown with configurable `max_comment_length`
- **H2 toolbar button**: Insert `## ` heading in the Markdown editor
- **422 error display**: Show validation error on `content` field instead of generic error
- **Comment count on articles**: Configurable toggle in settings, shown in card date line and article meta
- **Configurable max length**: `max_comment_length` setting in config and Filament UI
- **Preview route**: `POST /comments/preview` endpoint for client-side preview
- **ViewComment infolist**: Structured detail view with content, author, and metadata sections
- **Email-based moderation**: Signed URL approve/reject links in notification emails
- **Daily digest**: Configurable email digest of pending comments
- **Article meta stack**: `@stack('blogr-post-article-meta')` for extensibility

### 🐛 Bug Fixes

- **Namespace fix**: `Filament\Tables\Actions\BulkAction` → `Filament\Actions\BulkAction`
- **Parse error**: Typo `}` instead of `]` in Log::debug array close
- **Alpine null check**: Guard against undefined `replyTo` in Alpine component
- **Livewire registration**: Explicit `Livewire::component()` for `CommentSettings`
- **JSON API rewrite**: Return JSON from `CommentController`, rewrite Alpine frontend for JSON consumption
- **View composer**: Use `startPush` instead of `inject` for blog.show compatibility
- **Form tag**: Replace `x-filament-panels::form` with plain `<form>` tag
- **PHP 8.4**: Property type covariance for `navigationGroup`/`navigationIcon`, `BadgeColumn` → `TextColumn` with `badge()`
- **Gravatar**: Replace broken client-side `md5()` (Java hashCode) with server-side PHP MD5 hash

### ⬆️ Dependencies

- Added `scrivo/highlight.php ^9.18`

### ✅ Tests

- `CommentsViewTest`: 4 tests verifying Alpine nav config values and method existence
- Migrated all tests from `@test` annotations to `#[Test]` attributes (PHPUnit 12 / Pest 4.7 compatibility)
- Fixed `MissingAppKeyException` in testbench, fixed `created_at` mass-assignment in sort tests

## v1.0.0 - 2026-06-09

### ✨ Features

- **Threaded comments**: Nested replies with configurable depth (1–10 levels) and dedicated template partials
- **Voting system**: Up/down votes with IP-based anti-doublon protection, denormalized score column
- **Markdown formatting**: Limited safe Markdown (bold, italic, code blocks, inline code, blockquotes, links) with XSS-safe HTML rendering
- **3 moderation modes**: Pre-moderation (all pending), post-moderation (auto-publish), trust system (auto-trust after N approved comments)
- **Email notifications**: Configurable — new comment notification to site owner, reply notification to parent commenter, with opt-out links
- **Comment subscriptions**: Auto-subscribe on comment, unsubscribe via token link in email
- **Multi-layer anti-spam**: Cloudflare Turnstile (free, invisible), StopForumSpam API (free), Akismet API (paid, optional), local keyword/link filters
- **Rate limiting**: Per-IP throttling for comments (configurable per hour) and votes (per minute) via dedicated middleware
- **Filament admin interface**: CommentResource with list/view, status filters, bulk actions (approve/reject/spam/delete)
- **Filament settings page**: Full configuration UI for moderation, notifications, anti-spam, rate limits, nesting depth
- **Dashboard widgets**: Pending comments count with quick link to moderation list
- **Gravatar integration**: Automatic avatar display via MD5 email hash
- **Multilingual**: Complete EN, FR, ES, DE translations for frontend, admin, and email templates
- **ExtensionRegistry**: Registers as `blogr-comments` extension, respects enable/disable state
