<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('blogr-comments::messages.pending') }}
        </x-slot>

        <div class="flex items-center justify-between">
            <span class="text-3xl font-bold">
                {{ $this->getPendingCount() }}
            </span>

            <a href="{{ $this->getPendingUrl() }}" class="text-sm text-primary-600 hover:text-primary-500">
                {{ __('blogr-comments::messages.comments') }} →
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
