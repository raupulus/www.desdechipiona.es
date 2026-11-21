@extends('layouts.app')
@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            ✉️ {{ __('messages.contact') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">{{ __('messages.contact') }}</h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">{{ __('messages.contact_seo_description') }}</p>
    </div>
</header>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-10 shadow-xs">
            <form action="{{ route('contact.send') }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.contact_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror class="block w-full rounded-xl border border-slate-400 shadow-xs focus:border-orange-700 focus:ring-orange-700 text-base lg:text-sm" />
                    @error('name')<p id="name-error" role="alert" class="mt-1 text-sm text-red-700 font-bold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.contact_email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror class="block w-full rounded-xl border border-slate-400 shadow-xs focus:border-orange-700 focus:ring-orange-700 text-base lg:text-sm" />
                    @error('email')<p id="email-error" role="alert" class="mt-1 text-sm text-red-700 font-bold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="subject" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.contact_subject') }}</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="255" @error('subject') aria-invalid="true" aria-describedby="subject-error" @enderror class="block w-full rounded-xl border border-slate-400 shadow-xs focus:border-orange-700 focus:ring-orange-700 text-base lg:text-sm" />
                    @error('subject')<p id="subject-error" role="alert" class="mt-1 text-sm text-red-700 font-bold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="message" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.contact_message') }}</label>
                    <textarea name="message" id="message" rows="5" required maxlength="5000" @error('message') aria-invalid="true" aria-describedby="message-error" @enderror class="block w-full rounded-xl border border-slate-400 shadow-xs focus:border-orange-700 focus:ring-orange-700 text-base lg:text-sm">{{ old('message') }}</textarea>
                    @error('message')<p id="message-error" role="alert" class="mt-1 text-sm text-red-700 font-bold">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="privacy" id="privacy" value="1" required @error('privacy') aria-invalid="true" aria-describedby="privacy-error" @enderror class="rounded border-slate-400 text-orange-700 focus:ring-orange-600" />
                    <label for="privacy" class="ml-2 text-sm text-slate-700">{{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" class="text-orange-800 font-bold underline">{{ __('messages.contact_privacy_link') }}</a></label>
                </div>
                @error('privacy')<p id="privacy-error" role="alert" class="text-sm text-red-700 font-bold">{{ $message }}</p>@enderror
                <x-recaptcha-script action="contact" />
                <x-turnstile-widget action="contact" />
                @error('g-recaptcha-response')<p role="alert" class="text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                <button type="submit" :disabled="submitting" class="w-full bg-orange-700 text-white py-3.5 rounded-xl hover:bg-orange-800 transition font-extrabold disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">{{ __('messages.contact_send') }}</button>
            </form>
        </div>

        <aside class="lg:col-span-5 space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="font-serif text-xl font-extrabold text-slate-950">{{ __('messages.contact_sidebar_reasons_title') }}</h2>
                <p class="text-sm text-slate-700 leading-relaxed">{{ __('messages.contact_sidebar_reasons_intro') }}</p>
                <ul class="list-disc pl-5 space-y-2 text-sm text-slate-800 leading-relaxed">
                    <li>{!! __('messages.contact_reason_docs') !!}</li>
                    <li>{!! __('messages.contact_reason_errata') !!}</li>
                    <li>{!! __('messages.contact_reason_education') !!}</li>
                    <li>{!! __('messages.contact_reason_tech') !!}</li>
                </ul>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="font-serif text-xl font-extrabold text-slate-950">{{ __('messages.contact_channels_title') }}</h2>
                <div class="space-y-1.5 text-sm text-slate-800">
                    <p><strong>{{ __('messages.contact_channels_email_label') }}</strong> <a href="mailto:info@desdechipiona.es" class="font-bold text-blue-900 underline">info@desdechipiona.es</a></p>
                    <p><strong>{{ __('messages.contact_channels_author_label') }}</strong> <a href="mailto:public@raupulus.dev" class="font-bold text-blue-900 underline">public@raupulus.dev</a></p>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('messages.contact_channels_reply_note') }}</p>
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <p class="text-xs font-bold text-slate-800">{{ __('messages.contact_channels_suggest_cta') }}</p>
                    <a href="{{ route('suggestions.create') }}" class="inline-flex items-center justify-center w-full rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-orange-800 transition">
                        ✍️ {{ __('messages.contact_channels_suggest_btn') }}
                    </a>
                </div>
            </section>

            <section class="rounded-2xl border-2 border-amber-500 bg-amber-50/70 p-6 space-y-3">
                <h2 class="font-serif text-lg font-extrabold text-slate-950">⚖️ {{ __('messages.contact_disclaimer_title') }}</h2>
                <p class="text-sm text-slate-800 leading-relaxed">{!! __('messages.contact_disclaimer_body') !!}</p>
                <a href="{{ route('phones.index') }}" class="inline-flex items-center justify-center w-full rounded-xl border border-slate-900 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-slate-100 transition">
                    📞 {{ __('messages.contact_disclaimer_phones_btn') }}
                </a>
            </section>
        </aside>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-5">
        <h2 class="font-serif text-2xl font-extrabold text-slate-950">{{ __('messages.contact_faq_title') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border border-slate-200 bg-stone-50 p-5 space-y-2">
                <h3 class="font-serif text-base font-extrabold text-slate-950">{{ __('messages.contact_faq_q1') }}</h3>
                <p class="text-sm text-slate-700 leading-relaxed">{{ __('messages.contact_faq_a1') }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-stone-50 p-5 space-y-2">
                <h3 class="font-serif text-base font-extrabold text-slate-950">{{ __('messages.contact_faq_q2') }}</h3>
                <p class="text-sm text-slate-700 leading-relaxed">{{ __('messages.contact_faq_a2') }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-stone-50 p-5 space-y-2">
                <h3 class="font-serif text-base font-extrabold text-slate-950">{{ __('messages.contact_faq_q3') }}</h3>
                <p class="text-sm text-slate-700 leading-relaxed">{{ __('messages.contact_faq_a3') }}</p>
            </div>
        </div>
    </section>
</div>
@endsection
