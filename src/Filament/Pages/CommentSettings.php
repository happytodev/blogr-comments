<?php

namespace Happytodev\BlogrComments\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Happytodev\Blogr\Services\ExtensionRegistry;
use Illuminate\Support\Facades\Artisan;
use Filament\Schemas\Schema;

class CommentSettings extends Page
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Comments';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Comment Settings';

    protected string $view = 'blogr-comments::filament.pages.comment-settings';

    public static function shouldRegisterNavigation(): bool
    {
        if (! app()->has(ExtensionRegistry::class)) {
            return true;
        }

        return app(ExtensionRegistry::class)->isEnabled('blogr-comments');
    }

    public string $moderation_mode = 'post';
    public int $trust_threshold = 3;
    public bool $notify_new_comment = true;
    public bool $notify_reply = true;
    public string $captcha_provider = 'none';
    public string $turnstile_site_key = '';
    public string $turnstile_secret_key = '';
    public bool $stop_forum_spam = true;
    public bool $akismet_enabled = false;
    public string $akismet_api_key = '';
    public int $rate_limit_comments = 15;
    public int $rate_limit_votes = 30;
    public int $max_depth = 3;
    public bool $allow_self_vote = true;
    public bool $show_comment_count_on_cards = true;
    public bool $show_comment_count_on_articles = true;
    public string $admin_frequency = 'immediate';
    public string $digest_time = '09:00';
    public string $digest_day = 'monday';
    public int $max_comment_length = 5000;

    public function mount(): void
    {
        $config = config('blogr-comments', []);

        $this->moderation_mode = $config['moderation']['mode'] ?? 'post';
        $this->trust_threshold = $config['moderation']['trust_threshold'] ?? 3;
        $this->notify_new_comment = $config['notifications']['new_comment'] ?? true;
        $this->notify_reply = $config['notifications']['reply'] ?? true;
        $this->captcha_provider = $config['spam']['captcha']['provider'] ?? 'none';
        $this->turnstile_site_key = $config['spam']['captcha']['site_key'] ?? '';
        $this->turnstile_secret_key = $config['spam']['captcha']['secret_key'] ?? '';
        $this->stop_forum_spam = $config['spam']['stop_forum_spam'] ?? true;
        $this->akismet_enabled = $config['spam']['akismet']['enabled'] ?? false;
        $this->akismet_api_key = $config['spam']['akismet']['api_key'] ?? '';
        $this->rate_limit_comments = $config['rate_limit']['comments'] ?? 15;
        $this->rate_limit_votes = $config['rate_limit']['votes'] ?? 30;
        $this->max_depth = $config['threading']['max_depth'] ?? 3;
        $this->max_comment_length = $config['editing']['max_comment_length'] ?? 5000;
        $this->allow_self_vote = $config['voting']['allow_self_vote'] ?? true;
        $this->show_comment_count_on_cards = $config['display']['show_comment_count_on_cards'] ?? true;
        $this->show_comment_count_on_articles = $config['display']['show_comment_count_on_articles'] ?? true;
        $this->admin_frequency = $config['notifications']['admin_frequency'] ?? 'immediate';
        $this->digest_time = $config['notifications']['digest_time'] ?? '09:00';
        $this->digest_day = $config['notifications']['digest_day'] ?? 'monday';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Moderation')
                    ->schema([
                        Select::make('moderation_mode')
                            ->label('Moderation mode')
                            ->options([
                                'pre' => 'Pre-moderation (all comments require approval)',
                                'post' => 'Post-moderation (auto-publish, admin can unpublish)',
                                'trust' => 'Trust system (new commenters moderated, trusted auto-publish)',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('trust_threshold')
                            ->label('Trust threshold (approved comments)')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (callable $get) => $get('moderation_mode') === 'trust'),
                    ]),

                Section::make('Notifications')
                    ->schema([
                        Select::make('admin_frequency')
                            ->label('Admin notification frequency')
                            ->options([
                                'immediate' => 'Immediate (email per comment)',
                                'daily' => 'Daily digest',
                                'weekly' => 'Weekly digest',
                            ])
                            ->live(),
                        TextInput::make('digest_time')
                            ->label('Digest time (HH:MM)')
                            ->placeholder('09:00')
                            ->regex('/^\d{2}:\d{2}$/')
                            ->visible(fn (callable $get) => in_array($get('admin_frequency'), ['daily', 'weekly'])),
                        Select::make('digest_day')
                            ->label('Digest day')
                            ->options([
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ])
                            ->visible(fn (callable $get) => $get('admin_frequency') === 'weekly'),
                        Toggle::make('notify_new_comment')
                            ->label('Notify site owner of new comments (immediate)')
                            ->visible(fn (callable $get) => $get('admin_frequency') === 'immediate'),
                        Toggle::make('notify_reply')
                            ->label('Notify commenters when someone replies'),
                    ]),

                Section::make('Anti-spam')
                    ->schema([
                        Select::make('captcha_provider')
                            ->label('CAPTCHA provider')
                            ->options([
                                'turnstile' => 'Cloudflare Turnstile (free)',
                                'none' => 'No CAPTCHA',
                            ])
                            ->live(),
                        TextInput::make('turnstile_site_key')
                            ->label('Turnstile Site Key')
                            ->visible(fn (callable $get) => $get('captcha_provider') === 'turnstile'),
                        TextInput::make('turnstile_secret_key')
                            ->label('Turnstile Secret Key')
                            ->visible(fn (callable $get) => $get('captcha_provider') === 'turnstile'),
                        Toggle::make('stop_forum_spam')
                            ->label('Check StopForumSpam (free)'),
                        Toggle::make('akismet_enabled')
                            ->label('Enable Akismet (paid)')
                            ->live(),
                        TextInput::make('akismet_api_key')
                            ->label('Akismet API Key')
                            ->visible(fn (callable $get) => $get('akismet_enabled')),
                    ]),

                Section::make('Rate Limiting')
                    ->schema([
                        TextInput::make('rate_limit_comments')
                            ->label('Max comments per hour (per IP)')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('rate_limit_votes')
                            ->label('Max votes per minute (per IP)')
                            ->numeric()
                            ->minValue(1),
                    ]),

                Section::make('Display')
                    ->schema([
                        Toggle::make('show_comment_count_on_cards')
                            ->label('Show comment count on blog post cards'),
                        Toggle::make('show_comment_count_on_articles')
                            ->label('Show comment count on article pages'),
                    ]),

                Section::make('Voting')
                    ->schema([
                        Toggle::make('allow_self_vote')
                            ->label('Allow authors to vote on their own comments (like Reddit)'),
                    ]),

                Section::make('Threading')
                    ->schema([
                        TextInput::make('max_depth')
                            ->label('Max nesting depth')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                    ]),

                Section::make('Editing')
                    ->schema([
                        TextInput::make('max_comment_length')
                            ->label('Max comment length (characters)')
                            ->numeric()
                            ->minValue(100)
                            ->maxValue(100000),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = [
            'moderation' => [
                'mode' => $this->moderation_mode,
                'trust_threshold' => (int) $this->trust_threshold,
            ],
            'notifications' => [
                'new_comment' => $this->notify_new_comment,
                'reply' => $this->notify_reply,
                'admin_frequency' => $this->admin_frequency,
                'digest_time' => $this->digest_time,
                'digest_day' => $this->digest_day,
            ],
            'spam' => [
                'captcha' => [
                    'provider' => $this->captcha_provider,
                    'site_key' => $this->turnstile_site_key,
                    'secret_key' => $this->turnstile_secret_key,
                ],
                'stop_forum_spam' => $this->stop_forum_spam,
                'akismet' => [
                    'enabled' => $this->akismet_enabled,
                    'api_key' => $this->akismet_api_key,
                ],
            ],
            'rate_limit' => [
                'comments' => (int) $this->rate_limit_comments,
                'votes' => (int) $this->rate_limit_votes,
            ],
            'display' => [
                'show_comment_count_on_cards' => $this->show_comment_count_on_cards,
                'show_comment_count_on_articles' => $this->show_comment_count_on_articles,
            ],

            'voting' => [
                'allow_self_vote' => $this->allow_self_vote,
            ],

            'threading' => [
                'max_depth' => (int) $this->max_depth,
            ],

            'editing' => [
                'window_minutes' => (int) config('blogr-comments.editing.window_minutes', 15),
                'max_comment_length' => (int) $this->max_comment_length,
            ],
        ];

        $configPath = config_path('blogr-comments.php');
        $currentConfig = config('blogr-comments', []);
        $updatedConfig = array_merge($currentConfig, $data);

        $export = var_export($updatedConfig, true);

        file_put_contents($configPath, "<?php\n\nreturn {$export};\n");

        Artisan::call('config:clear');

        Notification::make()
            ->title('Comment settings saved!')
            ->success()
            ->send();
    }
}
