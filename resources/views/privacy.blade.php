@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.privacy') }}</h1>

    @forelse ($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p class="text-sm font-bold text-slate-700">{{ __('messages.privacy_default_text') }}</p>
            <h2>{{ __('messages.privacy_s1_title') }}</h2>
            <p>{!! __('messages.privacy_s1_body') !!}</p>
            <h2>{{ __('messages.privacy_s2_title') }}</h2>
            <p>{!! __('messages.privacy_s2_body') !!}</p>
            <h2>{{ __('messages.privacy_s3_title') }}</h2>
            <p>{!! __('messages.privacy_s3_body') !!}</p>
            <h2>{{ __('messages.privacy_s4_title') }}</h2>
            <p>{!! __('messages.privacy_s4_body') !!}</p>
        </div>
    @endforelse
</div>
@endsection
