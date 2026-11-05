@extends('layouts.app')
@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            ✉️ {{ __('messages.contact') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">{{ __('messages.contact') }}</h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed">{{ __('messages.contact_seo_description') }}</p>
    </div>
</header>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-10 shadow-xs">
        <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
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
            <button type="submit" class="w-full bg-orange-700 text-white py-3.5 rounded-xl hover:bg-orange-800 transition font-extrabold">{{ __('messages.contact_send') }}</button>
        </form>
    </div>
</div>
@endsection
