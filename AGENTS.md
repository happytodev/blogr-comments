# AGENTS.md

A Laravel/Filament package (`happytodev/blogr-comments`) providing comments functionality for Blogr CMS.

## Quick start

```bash
composer install
vendor/bin/pest            # run all tests
```

## Package identity

- **Laravel package** (`happytodev/blogr-comments`), not a standalone app.
- Auto-discovers via `BlogrCommentsServiceProvider` in `composer.json` `extra.laravel.providers`.
- Namespace: `Happytodev\BlogrComments` → `src/`.
- Requires PHP ^8.3, Laravel ^12.0, Filament ^4.0, Blogr ^1.3.
- Local path repo `../blogr` for `happytodev/blogr` (dev-only); CI strips it with `jq` before install.

## Commit policy

Do not commit anything without the user's explicit agreement. Only the user initiates commits, typically via the release-manager skill.

## TDD workflow

- When the user reports an error, fix it using TDD:
 write/update a test that reproduces the bug first, then fix the code.
- Every new feature must include TDD tests.
- Before each commit for a PHP file change, run `php -l` on the modified file(s).
- Always run `php artisan test --parallel` before handing back to the user.
- If server-side changes are needed (e.g. `php artisan migrate`, config changes, env updates), explicitly warn the user and ask for confirmation before modifying anything on their server.

## Testing

- **Pest** (via `orchestra/testbench`), no phpunit.xml needed.
- `vendor/bin/pest` — the only test command (no `composer test` script).
- `tests/Architecture/FilamentImportsTest.php` — verifies every `use Filament\...` import resolves, catches "Class not found" errors and dead imports after Filament version bumps.
- `tests/Architecture/FilamentImportsTest.php` — also reflects overridden method signatures against parent classes (caught the `Infolist→Schema` mismatch).
- New tests go in `Happytodev\BlogrComments\Tests` namespace.

## Architecture

| Layer | Location | Key files |
|-------|----------|-----------|
| Routes | `routes/web.php` | 4 endpoints: `GET/POST /comments/{postSlug}`, `/comments/{comment}/reply`, `/comments/{comment}/vote` |
| Controllers | `src/Http/Controllers/CommentController.php` | JSON API, constructor DI for `CommentService` + `SpamService` |
| Middleware | `src/Http/Middleware/ThrottleComments.php` | Rate-limits per IP; registered as `throttle.comments` |
| Models | `src/Models/Comment.php`, `CommentVote.php`, `CommentSubscription.php` | Table `blogr_comments` (and `_votes`, `_subscriptions`) |
| Services | `src/Services/CommentService.php`, `ModerationService.php`, `SpamService.php`, `CommentRenderer.php` | All injected via constructor |
| Filament | `src/Filament/Resources/CommentResource.php`, `Pages/CommentSettings.php`, `Widgets/PendingCommentsWidget.php` | Admin UI for moderation |
| Notifications | `src/Notifications/NewCommentNotification.php`, `ReplyNotification.php` | Mail via `Notification::route('mail', ...)` |
| Plugin | `src/BlogrCommentsPlugin.php` | Implements both `BlogrExtension` + `FilamentPlugin` |

## Key conventions

- All routes are under `web` middleware group + `throttle.comments` alias (defined in ServiceProvider).
- Voting is IP+UserAgent based (no auth). `vote_type` is `1` (up) or `-1` (down).
- Comment statuses: `pending`, `approved`, `rejected`, `spam`.
- Translates strings via `__('blogr-comments::messages.*')` — keys in `resources/lang/{en,fr,es,de}/`.
- Config published via `php artisan vendor:publish --tag=blogr-comments-config`.
- No npm/node; no build step. Pure PHP + Blade + Alpine.js inline in `comments.blade.php`.
- `composer.lock` is gitignored.
- CI runs on `main` branch; matrix: PHP 8.3 + 8.4, `prefer-stable`.
