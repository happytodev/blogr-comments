<?php

namespace Happytodev\BlogrComments\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleComments
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next, string $type = 'comments'): Response
    {
        $maxAttempts = config("blogr-comments.rate_limit.{$type}", 5);
        $decayMinutes = $type === 'votes' ? 1 : 60;

        $key = $type . '|' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            return back()->withErrors([
                'rate_limit' => __('blogr-comments::messages.rate_limit_exceeded', [
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
