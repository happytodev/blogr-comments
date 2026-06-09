<?php

namespace Happytodev\BlogrComments\Services;

use Illuminate\Support\Facades\Http;

class SpamService
{
    public function isSpam(string $content, string $authorEmail, string $ip): bool
    {
        if ($this->checkLocalFilters($content)) {
            return true;
        }

        if (config('blogr-comments.spam.stop_forum_spam', true)) {
            if ($this->checkStopForumSpam($ip, $authorEmail)) {
                return true;
            }
        }

        if (config('blogr-comments.spam.akismet.enabled', false)) {
            return $this->checkAkismet($content, $authorEmail, $ip);
        }

        return false;
    }

    public function verifyCaptcha(string $token): bool
    {
        $provider = config('blogr-comments.spam.captcha.provider', 'turnstile');

        if ($provider === 'none') {
            return true;
        }

        $secret = config('blogr-comments.spam.captcha.secret_key');

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secret,
            'response' => $token,
        ]);

        return $response->json('success', false);
    }

    protected function checkLocalFilters(string $content): bool
    {
        $maxLinks = config('blogr-comments.spam.local_filters.max_links', 2);
        $linkCount = preg_match_all('/https?:\/\/[^\s]+/', $content);

        if ($linkCount > $maxLinks) {
            return true;
        }

        $blockedKeywords = config('blogr-comments.spam.local_filters.blocked_keywords', []);

        foreach ($blockedKeywords as $keyword) {
            if (str_contains(strtolower($content), strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    protected function checkStopForumSpam(string $ip, string $email): bool
    {
        try {
            $response = Http::timeout(5)->get('https://api.stopforumspam.org/api', [
                'ip' => $ip,
                'email' => $email,
                'json' => true,
            ]);

            if (! $response->successful()) {
                return false;
            }

            $data = $response->json();

            $ipConfidence = $data['ip']['confidence'] ?? 0;
            $emailConfidence = $data['email']['confidence'] ?? 0;

            return $ipConfidence > 50 || $emailConfidence > 50;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkAkismet(string $content, string $authorEmail, string $ip): bool
    {
        $apiKey = config('blogr-comments.spam.akismet.api_key');

        if (empty($apiKey)) {
            return false;
        }

        $siteUrl = config('app.url');

        try {
            $response = Http::timeout(10)->asForm()->post("https://{$apiKey}.rest.akismet.com/1.1/comment-check", [
                'blog' => $siteUrl,
                'user_ip' => $ip,
                'user_agent' => request()->userAgent() ?? '',
                'comment_author' => '',
                'comment_content' => $content,
                'comment_type' => 'comment',
            ]);

            return $response->body() === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }
}
