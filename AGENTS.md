# Blogr Comments AGENTS.md

## ⚠️ Issue creation — MANDATORY

**Every user request for a bug fix or new feature MUST trigger a GitHub
issue before any code is written or proposed.** This ensures traceability.

- User says "there is a bug" → create issue with `--label bug`
- User says "I need a feature" → create issue with `--label feature`
- The issue is created via `gh issue create` immediately upon understanding the need
- The issue MUST be closed when the work is merged into `main` — the PR description MUST include `Closes #<issue_number>` to auto-close on merge
- Skipping this is a process error

## ⚠️ Commit policy — ZERO TOLERANCE

**NEVER commit, amend, tag, or push unless the user explicitly loads the
`release-manager` skill and requests a release.** All commits must go
through the `release-manager` workflow. Violating this rule is a process error.

## ⚠️ TDD requirement — ZERO TOLERANCE

**Every bug fix and every feature addition MUST be driven by tests written
first (TDD).** Run the test to confirm it fails before implementing, then run
it again to confirm it passes after.

### Naming convention

- **Bug regression tests**: `regression_<issue_number>_<description>`
- **Feature tests**: `feature_<description>`

### RED phase (mandatory before any implementation)

1. Write the test that proves the bug exists or validates the expected behavior
2. Run `vendor/bin/pest --filter <test_name>` — confirm it **fails** (RED)
3. This proves the test detects the problem

### GREEN phase

1. Implement the fix or feature
2. Run `vendor/bin/pest --filter <test_name>` — confirm it **passes** (GREEN)
3. **Anti-false-positive gate**: Comment out the new implementation code and re-run the test — it must fail again

## Project

A Laravel/Filament package (`happytodev/blogr-comments`) providing threaded comments
with moderation, voting, anti-spam, and email notifications for Blogr CMS.

## Resources

| File | Content |
|------|---------|
| [README.md](README.md) | Installation, prerequisites |

## Stack

- PHP 8.3+, Laravel 12.x, FilamentPHP v4, Pest PHP 4.0
- Testbench 10.x, in-memory SQLite
- Alpine.js inline (no build step)

## Package identity

- **Laravel package**, not a standalone app.
- Auto-discovers via `BlogrCommentsServiceProvider` in `composer.json`.
- Namespace: `Happytodev\BlogrComments` → `src/`.
- Requires PHP ^8.3, Laravel ^12.0, Filament ^4.0, Blogr ^1.3.
- Local path repo `../blogr` for dev; CI strips it with `jq` before install.

## Commands

```bash
vendor/bin/pest --no-coverage       # Run all tests
vendor/bin/pest tests/Feature/SpecificTest.php
php -l src/SomeFile.php             # Syntax check before commit
```

## Testing quirks

- **Pest** (via `orchestra/testbench`), no phpunit.xml needed
- `tests/Architecture/FilamentImportsTest.php` — verifies `use Filament\...` imports resolve
- `tests/Architecture/FilamentImportsTest.php` — reflects overridden method signatures against parent classes
- Feature tests must declare `uses()` individually (Pest.php only covers base TestCase)
- `TestCase` registers `Livewire\LivewireServiceProvider` for ComponentRegistry coverage
- CI runs PHP 8.3 + 8.4, `prefer-stable`

## Architecture

| Layer | Location | Key files |
|-------|----------|-----------|
| Routes | `routes/web.php` | 4 endpoints: CRUD + vote |
| Controllers | `src/Http/Controllers/CommentController.php` | JSON API |
| Middleware | `src/Http/Middleware/ThrottleComments.php` | Rate-limits per IP |
| Models | `src/Models/Comment.php`, `CommentVote.php`, `CommentSubscription.php` | |
| Services | `src/Services/CommentService.php`, `ModerationService.php`, `SpamService.php` | |
| Filament | `src/Filament/Resources/CommentResource.php`, `Pages/CommentSettings.php` | |
| Plugin | `src/BlogrCommentsPlugin.php` | Implements `BlogrExtension + FilamentPlugin` |

## Key conventions

- All routes are under `web` middleware group + `throttle.comments` alias
- Voting is IP+UserAgent based (no auth). `vote_type` is `1` (up) or `-1` (down)
- Comment statuses: `pending`, `approved`, `rejected`, `spam`
- Translates strings via `__('blogr-comments::messages.*')`
- Config published via `php artisan vendor:publish --tag=blogr-comments-config`
- No npm/node; no build step. Pure PHP + Blade + Alpine.js inline
- `composer.lock` is gitignored
- CI runs on `main` branch

## Filament v4 gotchas

- **Table actions**: Use `Filament\Actions\Action` (NOT `Filament\Tables\Actions`)
- **Livewire 419**: ALL Filament page components MUST be registered in `registerLivewireComponents()`
  in the service provider's `boot()` method. Currently registered: `CommentSettings`, `ListComments`, `ViewComment`.

## Release

See `.opencode/skills/release-manager/SKILL.md`. Uses `gh release create`.
