<?php

use Happytodev\BlogrComments\Filament\Pages\CommentSettings;
use Happytodev\BlogrComments\Services\SettingsRepository;
use Illuminate\Support\Facades\DB;

/**
 * Issue #3 — the admin settings must live in the database, not in the config
 * file (replaced at every deployment by the repository's copy).
 */
beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

    app(SettingsRepository::class)->flush();
});

it('regression_3_stores_an_admin_save_in_the_database_instead_of_the_config_file', function () {
    $configFile = config_path('blogr-comments.php');
    $before = file_exists($configFile) ? md5_file($configFile) : null;

    $page = new CommentSettings;
    $page->mount();
    $page->moderation_mode = 'pre';
    $page->allow_self_vote = false;
    $page->save();

    // One row per setting, under its dotted configuration path.
    expect(DB::table(SettingsRepository::TABLE)->where('key', 'moderation.mode')->value('value'))
        ->toBe('"pre"')
        ->and(DB::table(SettingsRepository::TABLE)->where('key', 'voting.allow_self_vote')->value('value'))
        ->toBe('false');

    // And the values apply at once, so the page reflects the save.
    expect(config('blogr-comments.moderation.mode'))->toBe('pre')
        ->and(config('blogr-comments.voting.allow_self_vote'))->toBeFalse();

    // The configuration file is no longer rewritten.
    expect(file_exists($configFile) ? md5_file($configFile) : null)->toBe($before);
});

it('regression_3_applies_the_stored_settings_on_top_of_the_configuration', function () {
    config(['blogr-comments.moderation.mode' => 'post']);
    config(['blogr-comments.threading.max_depth' => 3]);

    DB::table(SettingsRepository::TABLE)->insert([
        'key' => 'moderation.mode',
        'value' => '"trust"',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $repository = app(SettingsRepository::class);
    $repository->flush();
    $repository->applyToConfig();

    expect(config('blogr-comments.moderation.mode'))->toBe('trust')     // la base gagne
        ->and(config('blogr-comments.threading.max_depth'))->toBe(3);   // le fichier reste
});

it('regression_3_never_stores_a_null_value', function () {
    // A page saved without being mounted carries default values.
    (new CommentSettings)->save();

    expect(DB::table(SettingsRepository::TABLE)->pluck('value'))->not->toContain('null');

    // A null passed to the repository means "not filled in": it is ignored, no row
    // is created for it, and the other values go through.
    app(SettingsRepository::class)->put([
        'notifications.owner_email' => null,
        'moderation.mode' => 'pre',
    ]);

    expect(DB::table(SettingsRepository::TABLE)->where('key', 'notifications.owner_email')->exists())->toBeFalse()
        ->and(DB::table(SettingsRepository::TABLE)->where('key', 'moderation.mode')->value('value'))->toBe('"pre"');
});

it('regression_3_does_not_bake_the_overrides_into_the_compiled_configuration', function () {
    DB::table(SettingsRepository::TABLE)->insert([
        'key' => 'threading.max_depth',
        'value' => '9',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config(['blogr-comments.threading.max_depth' => 3]);

    // `php artisan config:cache` boots the application before freezing the
    // configuration: applying the overrides there would bake them into the
    // compiled file, which would then take precedence over the table.
    $_SERVER['argv'] = ['artisan', 'config:cache'];

    try {
        app(SettingsRepository::class)->applyToConfig();
    } finally {
        unset($_SERVER['argv']);
    }

    expect(config('blogr-comments.threading.max_depth'))->toBe(3);
});

it('regression_3_forgets_the_stored_settings_on_reset', function () {
    $repository = app(SettingsRepository::class);
    $repository->put(['moderation.mode' => 'pre']);
    $repository->put(['threading.max_depth' => 5]);

    expect($repository->count())->toBe(2)
        ->and($repository->forgetAll())->toBe(2)
        ->and(DB::table(SettingsRepository::TABLE)->count())->toBe(0)
        ->and(app(SettingsRepository::class)->all())->toBe([]);
});

it('regression_3_falls_back_to_the_config_file_while_the_table_is_missing', function () {
    app()->instance(SettingsRepository::class, new class extends SettingsRepository
    {
        public function hasTable(): bool
        {
            return false;
        }
    });

    $page = new CommentSettings;
    $page->mount();
    $page->moderation_mode = 'pre';

    $page->save();

    // Until the table is migrated, the previous behaviour is kept (in the test
    // environment, the fallback applies the values in memory).
    expect(config('blogr-comments.moderation.mode'))->toBe('pre')
        ->and(DB::table(SettingsRepository::TABLE)->count())->toBe(0);
});
