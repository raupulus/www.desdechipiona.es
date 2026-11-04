<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('filament.clear_cache') }}
        </x-slot>
        <p style="margin: 0; font-size: 0.875rem; opacity: 0.85;">
            {{ __('filament.clear_cache_desc') }}
        </p>
        <div style="margin-top: 1rem; padding: 1rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.1);">
            <p style="margin: 0; font-size: 0.875rem; font-weight: 600; color: #d97706;">⚠️ {{ __('filament.clear_cache_warning') }}</p>
            <ul style="margin: 0.5rem 0 0 0; padding-left: 1.25rem; font-size: 0.875rem; list-style-type: disc; opacity: 0.9;">
                <li>{{ __('filament.clear_cache_warn_sessions') }}</li>
                <li>{{ __('filament.clear_cache_warn_data') }}</li>
                <li>{{ __('filament.clear_cache_warn_queues') }}</li>
                <li>{{ __('filament.clear_cache_warn_slow') }}</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
