@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.about_title') }}</h1>

    @forelse($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p>
                {!! __('messages.about_p1') !!}
            </p>
            <p>
                {!! __('messages.about_p2') !!}
            </p>
            <p>
                {{ __('messages.about_p3') }}
            </p>
        </div>
    @endforelse
</div>
@endsection
