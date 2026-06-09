# Changelog

All notable changes to `blogr-comments` will be documented in this file.

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
