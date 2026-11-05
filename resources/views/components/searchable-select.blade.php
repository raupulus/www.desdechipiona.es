@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'options' => [], // array asociativo [valor => etiqueta] o array de arrays [['value' => ..., 'label' => ...]]
    'required' => false,
    'class' => '',
])

@php
    $placeholderText = $placeholder ?? __('messages.select_option_placeholder');
    $accessibleLabel = $label ?? $placeholderText;
    $inputId = $id ?? $name;
    $normalizedOptions = [];
    foreach ($options as $optKey => $optVal) {
        if (is_array($optVal) && isset($optVal['value'], $optVal['label'])) {
            $normalizedOptions[] = [
                'value' => (string) $optVal['value'],
                'label' => (string) $optVal['label'],
            ];
        } else {
            $normalizedOptions[] = [
                'value' => (string) $optKey,
                'label' => (string) $optVal,
            ];
        }
    }

    $currentValue = (string) (old($name, $value) ?? '');
    $initialLabel = '';
    foreach ($normalizedOptions as $opt) {
        if ($opt['value'] === $currentValue) {
            $initialLabel = $opt['label'];
            break;
        }
    }
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selected: @js($currentValue),
        selectedLabel: @js($initialLabel),
        options: @js($normalizedOptions),
        focusedIndex: -1,
        get filteredOptions() {
            if (!this.search.trim()) return this.options;
            const q = this.search.toLowerCase().trim();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        select(opt) {
            this.selected = opt.value;
            this.selectedLabel = opt.label;
            this.open = false;
            this.search = '';
            this.focusedIndex = -1;
            $dispatch('change', this.selected);
            $nextTick(() => { $refs.triggerButton?.focus(); });
        },
        clear() {
            this.selected = '';
            this.selectedLabel = '';
            this.open = false;
            this.search = '';
            this.focusedIndex = -1;
            $dispatch('change', '');
            $nextTick(() => { $refs.triggerButton?.focus(); });
        },
        onKeyDown(e) {
            if (!this.open) {
                if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                    this.open = true;
                    e.preventDefault();
                }
                return;
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.focusedIndex = (this.focusedIndex + 1) % this.filteredOptions.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.focusedIndex = (this.focusedIndex - 1 + this.filteredOptions.length) % this.filteredOptions.length;
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.focusedIndex >= 0 && this.focusedIndex < this.filteredOptions.length) {
                    this.select(this.filteredOptions[this.focusedIndex]);
                }
            } else if (e.key === 'Escape') {
                this.open = false;
                this.search = '';
                $nextTick(() => { $refs.triggerButton?.focus(); });
            }
        }
    }"
    x-init="$watch('open', val => {
        if (val) {
            $nextTick(() => { $refs.searchInput?.focus(); });
        }
    })"
    @keydown="onKeyDown($event)"
    @click.outside="open = false; search = ''"
    class="relative w-full {{ $class }}"
>
    {{-- Input nativo oculto para envío transparente en formularios GET/POST --}}
    <input
        type="hidden"
        name="{{ $name }}"
        :value="selected"
    >

    {{-- Botón disparador visible y botón hermano de limpieza --}}
    <div class="relative flex items-center">
        <button
            type="button"
            id="{{ $inputId }}"
            x-ref="triggerButton"
            @click="open = !open"
            role="combobox"
            aria-label="{{ $accessibleLabel }}"
            aria-controls="{{ $inputId }}-listbox"
            :aria-activedescendant="open && focusedIndex >= 0 ? '{{ $inputId }}-opt-' + focusedIndex : null"
            :aria-expanded="open"
            aria-haspopup="listbox"
            @if($required) aria-required="true" @endif
            class="w-full flex items-center justify-between gap-2 rounded-xl border-2 border-slate-400 bg-white px-3.5 py-2.5 pr-14 text-left text-base lg:text-sm font-bold text-slate-950 shadow-xs hover:border-slate-500 focus:border-orange-700 focus:ring-2 focus:ring-orange-700 transition cursor-pointer"
        >
            <span class="truncate" :title="selectedLabel" x-text="selectedLabel || @js($placeholderText)" :class="!selectedLabel ? 'text-slate-600 font-medium' : 'text-slate-950 font-bold'"></span>
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 flex items-center text-slate-600">
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180 text-orange-700' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <template x-if="selected && !{{ $required ? 'true' : 'false' }}">
            <button
                type="button"
                aria-label="{{ __('messages.clear_selection_title') }}"
                @click.stop="clear()"
                title="{{ __('messages.clear_selection_title') }}"
                class="absolute right-8 top-1/2 -translate-y-1/2 inline-flex h-6 w-6 items-center justify-center rounded-full hover:bg-slate-200 text-slate-600 hover:text-slate-950 cursor-pointer focus:outline-none focus:ring-2 focus:ring-orange-700"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </template>
    </div>

    {{-- Dropdown con buscador y lista de opciones --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 right-0 z-50 mt-1.5 rounded-2xl border-2 border-slate-900 bg-white p-2 shadow-2xl space-y-2"
    >
        {{-- Campo de búsqueda para filtrar al escribir --}}
        <div class="relative">
            <input
                x-ref="searchInput"
                type="text"
                x-model="search"
                aria-label="{{ __('messages.type_to_search') }}"
                placeholder="{{ __('messages.type_to_search') }}"
                class="w-full rounded-xl border border-slate-400 bg-slate-50 px-3 py-2 text-base lg:text-sm font-medium text-slate-950 placeholder:text-slate-600 focus:border-orange-700 focus:bg-white focus:ring-1 focus:ring-orange-700"
            >
            <template x-if="search">
                <button
                    type="button"
                    aria-label="{{ __('messages.clear_selection_title') }}"
                    @click="search = ''; $refs.searchInput.focus()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-950"
                >
                    ✕
                </button>
            </template>
        </div>

        {{-- Opciones filtradas con teclado accesible --}}
        <ul id="{{ $inputId }}-listbox" role="listbox" aria-label="{{ $accessibleLabel }}" class="max-h-60 overflow-y-auto space-y-1 overscroll-contain pr-1">
            <template x-for="(opt, idx) in filteredOptions" :key="opt.value">
                <li
                    :id="'{{ $inputId }}-opt-' + idx"
                    role="option"
                    :aria-selected="selected === opt.value"
                    @click="select(opt)"
                    @mouseenter="focusedIndex = idx"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-semibold cursor-pointer transition"
                    :class="{
                        'bg-orange-700 text-white font-extrabold': selected === opt.value,
                        'bg-orange-50 text-orange-950': focusedIndex === idx && selected !== opt.value,
                        'text-slate-950 hover:bg-slate-100': selected !== opt.value && focusedIndex !== idx
                    }"
                >
                    <span x-text="opt.label" class="leading-snug break-words pr-2"></span>
                    <template x-if="selected === opt.value">
                        <svg class="h-4 w-4 shrink-0 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </template>
                </li>
            </template>

            <template x-if="filteredOptions.length === 0">
                <li class="px-3 py-4 text-center text-xs font-medium text-slate-600">
                    {{ __('messages.no_matches_for') }} "<span x-text="search" class="font-bold"></span>".
                </li>
            </template>
        </ul>
    </div>
</div>
