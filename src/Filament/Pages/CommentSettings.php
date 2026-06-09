<?php

namespace Happytodev\BlogrComments\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Artisan;

class CommentSettings extends Page
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Comments';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Comment Settings';

    protected string $view = 'blogr-comments::filament.pages.comment-settings';

    // Moderation
    public string $moderation_mode = 'post';
    public int $trust_threshold = 3;

    // Notifications
    public bool $notify_new_comment = true;
    public bool $notify_reply = true;

    // Spam
    public string $captcha_provider = 'turnstile';
    public string $turnstile_site_key = '';
    public string $turnstile_secret_key = '';
    public bool $stop_forum_spam = true;
    public bool $akismet_enabled = false;
    public string $akismet_api_key = '';

    // Rate limit
    public int $rate_limit_comments = 5;
    public int $rate_limit_votes = 30;

    // Threading
    public int $max_depth = 3;

    public function mount(): void
    {
        $config = config('blogr-comments', []);

        $this->moderation_mode = $config['moderation']['mode'] ?? 'post';
        $this->trust_threshold = $config['moderation']['trust_threshold'] ?? 3;
        $this->notify_new_comment = $config['notifications']['new_comment'] ?? true;
        $this->notify_reply = $config['notifications']['reply'] ?? true;
        $this->captcha_provider = $config['spam']['captcha']['provider'] ?? 'turnstile';
        $this->turnstile_site_key = $config['spam']['captcha']['site_key'] ?? '';
        $this->turnstile_secret_key = $config['spam']['captcha']['secret_key'] ?? '';
        $this->stop_forum_spam = $config['spam']['stop_forum_spam'] ?? true;
        $this->akismet_enabled = $config['spam']['akismet']['enabled'] ?? false;
        $this->akismet_api_key = $config['spam']['akismet']['api_key'] ?? '';
        $this->rate_limit_comments = $config['rate_limit']['comments'] ?? 5;
        $this->rate_limit_votes = $config['rate_limit']['votes'] ?? 30;
        $this->max_depth = $config['threading']['max_depth'] ?? 3;
    }

    public function getFormSchema(): array
    {
        return [
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
                        ->visible(fn () => $this->moderation_mode === 'trust'),
                ]),

            Section::make('Notifications')
                ->schema([
                    Toggle::make('notify_new_comment')
                        ->label('Notify site owner of new comments'),
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
                        ->visible(fn () => $this->captcha_provider === 'turnstile'),
                    TextInput::make('turnstile_secret_key')
                        ->label('Turnstile Secret Key')
                        ->visible(fn () => $this->captcha_provider === 'turnstile'),
                    Toggle::make('stop_forum_spam')
                        ->label('Check StopForumSpam (free)'),
                    Toggle::make('akismet_enabled')
                        ->label('Enable Akismet (paid)')
                        ->live(),
                    TextInput::make('akismet_api_key')
                        ->label('Akismet API Key')
                        ->visible(fn () => $this->akismet_enabled),
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

            Section::make('Threading')
                ->schema([
                    TextInput::make('max_depth')
                        ->label('Max nesting depth')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10),
                ]),
        ];
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
            'threading' => [
                'max_depth' => (int) $this->max_depth,
            ],
        ];

        $this->updateConfigFile($data);

        Artisan::call('config:clear');

        Notification::make()
            ->title('Comment settings saved!')
            ->success()
            ->send();
    }

    private function updateConfigFile(array $data): void
    {
        $configPath = config_path('blogr-comments.php');
        $currentConfig = config('blogr-comments', []);
        $updatedConfig = array_merge($currentConfig, $data);

        $export = var_export($updatedConfig, true);
        $export = preg_replace('/^\\s+/m', '        ', $export);
        $export = preg_replace('/array \\(/', '[', $export);
        $export = preg_replace('/\\)/', ']', $export);
        $export = preg_replace('/=>\\s*\\n\\s*\\[/', '=> [', $export);

        file_put_contents($configPath, "<?php\n\nreturn {$export};\n");
    }
}
