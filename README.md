# Blogr Comments

[![Latest Version](https://img.shields.io/packagist/v/happytodev/blogr-comments.svg?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![Tests](https://img.shields.io/github/actions/workflow/status/happytodev/blogr-comments/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/happytodev/blogr-comments/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/happytodev/blogr-comments?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![Total Downloads](https://img.shields.io/packagist/dt/happytodev/blogr-comments?style=flat-square)](https://packagist.org/packages/happytodev/blogr-comments)
[![GitHub Stars](https://img.shields.io/github/stars/happytodev/blogr-comments?style=flat-square)](https://github.com/happytodev/blogr-comments)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

**A full-featured comment system for Blogr CMS** — threaded comments, moderation, voting, anti-spam, and email notifications.

---

## Features

- **Threaded comments** — Nested replies with configurable depth (up to 10 levels)
- **Voting system** — Up/down votes with anti-doublon by IP
- **Markdown formatting** — Bold, italic, code blocks, links, blockquotes
- **3 moderation modes** — Pre-moderation, post-moderation, or trust system
- **Anti-spam multilayered** — Cloudflare Turnstile + StopForumSpam + Akismet (optional)
- **Rate limiting** — Configurable per-IP limits for comments and votes
- **Email notifications** — New comment (to owner) + reply notifications (to commenters)
- **Filament admin** — Full moderation interface with bulk actions and dashboard widgets
- **Gravatar support** — Automatic avatars via email hash
- **Multilingual** — EN, FR, ES, DE included

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
- View comment details

### Admin — Settings

Go to **Comments → Settings** to configure:
- Moderation mode (pre/post/trust)
- Email notifications (owner + replies)
- Anti-spam providers
- Rate limits
- Nesting depth

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
