<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- Texto descriptivo --}}
        <div
            style="display: flex; align-items: flex-start; gap: 0.875rem; padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(59, 130, 246, 0.35); background: rgba(59, 130, 246, 0.08);"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" style="width: 1.25rem; height: 1.25rem; flex-shrink: 0; margin-top: 0.125rem; color: #2563eb;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <p style="margin: 0; font-size: 0.875rem; line-height: 1.6;">
                {{ __('messages.api_tokens_description') }}
            </p>
        </div>

        {{-- Formulario de creación de token --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('messages.api_tokens_create_title') }}
            </x-slot>

            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-start;">
                <div style="flex: 1 1 16rem; min-width: 0;">
                    <label for="token-name-input" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">{{ __('messages.api_tokens_col_name') }}</label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            id="token-name-input"
                            type="text"
                            wire:model="tokenName"
                            wire:keydown.enter="createToken"
                            placeholder="{{ __('messages.api_tokens_placeholder') }}"
                            maxlength="255"
                        />
                    </x-filament::input.wrapper>
                    @error('tokenName')
                        <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #dc2626;" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <x-filament::button wire:click="createToken" wire:loading.attr="disabled" icon="heroicon-m-plus">
                    <span wire:loading.remove wire:target="createToken">{{ __('messages.create') }}</span>
                    <span wire:loading wire:target="createToken">...</span>
                </x-filament::button>
            </div>

            {{-- Token recién generado --}}
            @if($plainTextToken)
                <div
                    style="margin-top: 1.25rem; padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08);"
                >
                    <p style="margin: 0 0 0.75rem 0; font-size: 0.8125rem; font-weight: 600; color: #059669;">
                        {{ __('messages.api_tokens_created_msg') }}
                    </p>
                    <div
                        x-data="{ copied: false }"
                        style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;"
                    >
                        <x-filament::button
                            color="success"
                            size="sm"
                            icon="heroicon-m-clipboard-document"
                            x-on:click="navigator.clipboard.writeText(@js($plainTextToken)).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                        >
                            <span x-text="copied ? @js(__('messages.api_tokens_copied')) : @js(__('messages.api_tokens_copy'))">{{ __('messages.api_tokens_copy') }}</span>
                        </x-filament::button>
                        <code
                            style="flex: 1 1 14rem; font-size: 0.8125rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; word-break: break-all; user-select: all; padding: 0.5rem 0.75rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.14);"
                        >{{ $plainTextToken }}</code>
                    </div>
                </div>
            @endif
        </x-filament::section>

        {{-- Listado de tokens existentes --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('messages.api_tokens_list_title') }}
            </x-slot>

            @if($this->tokens->isEmpty())
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1rem; text-align: center;">
                    <div class="bg-gray-100 dark:bg-white/5" style="display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: 9999px; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="text-gray-400 dark:text-gray-500" style="width: 1.5rem; height: 1.5rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400" style="margin: 0; font-size: 0.875rem; font-weight: 500;">
                        {{ __('messages.api_tokens_empty') }}
                    </p>
                </div>
            @else
                <div style="overflow-x: auto; margin: -1rem -1.5rem;">
                    <table style="width: 100%; min-width: 560px; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(156, 163, 175, 0.25); background: rgba(156, 163, 175, 0.06);">
                                <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('messages.api_tokens_col_name') }}
                                </th>
                                <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('messages.api_tokens_col_created') }}
                                </th>
                                <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('messages.api_tokens_col_last_used') }}
                                </th>
                                <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('messages.api_tokens_col_expires') }}
                                </th>
                                <th style="padding: 0.875rem 1.25rem; text-align: right; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('messages.api_tokens_col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->tokens as $token)
                                <tr style="border-bottom: 1px solid rgba(156, 163, 175, 0.15);">
                                    {{-- Nombre --}}
                                    <td style="padding: 1rem 1.25rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="bg-indigo-50 dark:bg-indigo-500/10" style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="text-indigo-600 dark:text-indigo-400" style="width: 1rem; height: 1rem;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                                </svg>
                                            </div>
                                            <span class="text-gray-950 dark:text-white" style="font-weight: 600; font-size: 0.875rem; word-break: break-all;">{{ $token->name }}</span>
                                        </div>
                                    </td>

                                    {{-- Creado --}}
                                    <td class="text-gray-600 dark:text-gray-400" style="padding: 1rem 1.25rem; font-size: 0.8125rem; white-space: nowrap;">
                                        {{ $token->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>

                                    {{-- Último uso --}}
                                    <td style="padding: 1rem 1.25rem; font-size: 0.8125rem; white-space: nowrap;">
                                        @if($token->last_used_at)
                                            <span class="text-gray-600 dark:text-gray-400">{{ $token->last_used_at->translatedFormat('d M Y, H:i') }}</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500" style="font-style: italic;">{{ __('messages.api_tokens_never') }}</span>
                                        @endif
                                    </td>

                                    {{-- Expiración --}}
                                    <td style="padding: 1rem 1.25rem; font-size: 0.8125rem; white-space: nowrap;">
                                        @if($token->expires_at)
                                            @if($token->expires_at->isPast())
                                                <x-filament::badge color="danger">
                                                    {{ $token->expires_at->translatedFormat('d M Y') }}
                                                </x-filament::badge>
                                            @else
                                                <span class="text-gray-600 dark:text-gray-400">{{ $token->expires_at->translatedFormat('d M Y') }}</span>
                                            @endif
                                        @else
                                            <x-filament::badge color="gray">
                                                {{ __('messages.api_tokens_no_expiry') }}
                                            </x-filament::badge>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td style="padding: 1rem 1.25rem; text-align: right;">
                                        <x-filament::icon-button
                                            color="danger"
                                            icon="heroicon-m-trash"
                                            size="sm"
                                            wire:click="deleteToken({{ $token->id }})"
                                            wire:confirm="{{ __('messages.api_tokens_delete_confirm') }}"
                                            wire:loading.attr="disabled"
                                            :tooltip="__('messages.delete')"
                                            :label="__('messages.delete')"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Contador de tokens --}}
                    <div class="border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]" style="padding: 0.75rem 1.25rem;">
                        <p class="text-gray-500 dark:text-gray-400" style="margin: 0; font-size: 0.75rem;">
                            {{ $this->tokens->count() }} {{ $this->tokens->count() === 1 ? 'token' : 'tokens' }}
                        </p>
                    </div>
                </div>
            @endif
        </x-filament::section>

    </div>
</x-filament-panels::page>
