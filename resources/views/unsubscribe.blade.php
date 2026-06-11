<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('blogr-comments::messages.unsubscribed_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
        <div class="text-4xl mb-4">&#10003;</div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            {{ __('blogr-comments::messages.unsubscribed_title') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ __('blogr-comments::messages.unsubscribed_body', ['email' => $email]) }}
        </p>
        <a href="{{ url('/') . '/' . $postSlug }}" class="inline-block px-6 py-3 bg-[var(--color-primary,#3b82f6)] text-white rounded-lg hover:bg-[var(--color-primary-hover,#2563eb)] transition-colors">
            {{ __('blogr-comments::messages.back_to_post') }}
        </a>
    </div>
</body>
</html>
