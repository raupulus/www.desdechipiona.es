@extends('layouts.app')
@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            📝 {{ __('messages.blog') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">{{ __('messages.blog') }}</h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            {{ __('messages.blog_seo_description') }}
        </p>
    </div>
</header>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <p class="text-slate-800 font-bold col-span-3">{{ __('messages.no_results') }}</p>
        @endforelse
    </div>
    <x-pagination :paginator="$posts" />
</div>
@endsection
