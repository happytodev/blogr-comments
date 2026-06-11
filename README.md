# Blogr Comments

[![Latest Version](https://img.shields.io/packagist/v/happytodev/blogr-comments.svg?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![Tests](https://img.shields.io/github/actions/workflow/status/happytodev/blogr-comments/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/happytodev/blogr-comments/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/happytodev/blogr-comments?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![Total Downloads](https://img.shields.io/packagist/dt/happytodev/blogr-comments?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![GitHub Stars](https://img.shields.io/github/stars/happytodev/blogr-comments?style=flat-square)](https://github.com/happytodev/blogr-comments)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

**A full-featured comment system for Blogr CMS** — threaded comments, moderation, voting, anti-spam, email notifications, and a complete Filament admin interface.

## Features

### 🔧 Core
- **Threaded comments** — Nested replies with configurable depth (1–10 levels) and dedicated partials for each level
- **Markdown formatting** — Bold, italic, code blocks (with syntax highlighting via `scrivo/highlight.php`), inline code, links, H2
- **Live preview** — See formatted content before posting
- **Character counter** — Configurable max length (default 5000) with real-time countdown
- **Toolbar** — Bold, italic, H2, link, code block, preview buttons (safe Markdown subset)

### 🛡️ Moderation & Anti-spam
- **3 moderation modes** — Pre-moderation (all pending), post-moderation (auto-publish), trust system (auto-trust after N approved comments)
- **Multi-layer anti-spam** — Cloudflare Turnstile (free, invisible), StopForumSpam API (free), Akismet API (paid, optional), local keyword/link filters
- **Rate limiting** — Per-IP throttling for comments (configurable per hour) and votes (per minute) via dedicated middleware
- **Admin moderation** — Approve, reject, mark as spam, or delete individually or in bulk
- **Email-based quick moderation** — Signed URL approve/reject links in notification emails

### 📬 Notifications
- **New comment alert** — Notify site owner on every new comment (or daily digest or weekly digest)
- **Reply notification** — Possibility to auto-notify parent commenter when someone replies, with opt-out link
- **Comment subscriptions** — Auto-subscribe on comment for reply notifications
- **Daily digest** — Configurable email digest of pending comments

### 🗳️ Voting
- **Up/down voting** — IP + User-Agent based anti-doublon protection
- **Denormalized score** — `vote_score` column on comments for fast sorting
- **Sort options** — Newest, oldest, best (by score)

### ⚙️ Filament Admin
- **Comment Resource** — Full list/view with status filters, visible/invisible toggle
- **Dashboard widget** — Pending comments count with quick link to moderation list
- **Settings page** — All configuration UI (moderation, notifications, anti-spam, rate limits, nesting depth, display options)

### 🌍 Frontend Display
- **Syntax highlighting** — Server-side code highlighting with language badge, dark mode support
- **Per-comment permalink** — 🔗 button copies sharable URL with `#comment-{id}` anchor
- **Smart anchor scrolling** — Dynamic scroll offset accounts for fixed/sticky navigation and mobile viewport
- **Comment count on articles** — Shows count in card date line and article metadata (configurable)
- **Gravatar support** — Automatic avatars via email MD5 hash
- **Multilingual** — Complete EN, FR, ES, DE translations for frontend, admin, and email templates

### 🔌 Extension Points
- **BlogrExtension interface** — Registers as `blogr-comments` extension, respects enable/disable state
- **FilamentPlugin** — Auto-registers resource, settings page, and widget in the panel

## Requirements

- PHP ^8.3
- Blogr ^1.3
- Laravel ^12.0
- Filament ^4.0

## Installation

```bash
composer require happytodev/blogr-comments
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=blogr-comments-config
```

### Anti-spam (Turnstile)

Get your free Turnstile keys from [Cloudflare Turnstile](https://developers.cloudflare.com/turnstile/) and add to your `.env`:

```env
TURNSTILE_SITE_KEY=your_site_key
TURNSTILE_SECRET_KEY=your_secret_key
```

### Anti-spam (Akismet — optional)

```env
AKISMET_API_KEY=your_akismet_key
```

## Usage

### Frontend

Comments automatically appear at the bottom of each blog post. No additional setup needed.

### Admin — Moderation

Navigate to **Comments** in your Filament admin sidebar to:
- View all comments sorted by status
- Filter by status (pending, approved, rejected, spam)
- Approve, reject, or mark as spam (individual or bulk)
- View comment details with full metadata

### Admin — Settings

Go to **Comments → Settings** to configure:
- Moderation mode (pre/post/trust)
- Email notifications (owner + replies + digest)
- Anti-spam providers
- Rate limits
- Nesting depth
- Display options (comment count on articles)

## Testing

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
