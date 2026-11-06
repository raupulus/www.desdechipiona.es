@extends('layouts.app')
@section('content')
@php
    $title = $post->getTranslation('title', app()->getLocale()) ?: $post->getTranslation('title', 'es');
@endphp
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $title }}" width="1200" height="675" fetchpriority="high" decoding="async" class="w-full h-64 md:h-96 object-cover rounded-2xl mb-8" />@endif
    <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-slate-950 mb-4 leading-tight">{{ $title }}</h1>
    <div class="flex flex-wrap items-center text-sm font-bold text-slate-700 mb-8 gap-4">
        @if($post->user)<span>{{ $post->user->name }}</span>@endif
        @if($post->published_at)<time datetime="{{ $post->published_at->toIso8601String() }}">{{ format_date($post->published_at) }}</time>@endif
        @foreach($post->categories as $cat)<span class="bg-amber-100 text-amber-950 px-2.5 py-1 rounded-lg text-xs font-extrabold">{{ $cat->getTranslation('name', app()->getLocale()) }}</span>@endforeach
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none">{!! $post->rendered_content !!}</div>
    <div class="mt-10"><a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-sm font-extrabold text-orange-800 hover:underline"><span aria-hidden="true">&larr;</span> {{ __('messages.back') }}</a></div>
</article>
@endsection
