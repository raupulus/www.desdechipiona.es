@props(['post'])
@php
    $postTitle = $post->getTranslation('title', app()->getLocale());
@endphp
<article class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden hover:border-orange-700 hover:shadow-md transition">
    @if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $postTitle }}" loading="lazy" decoding="async" width="800" height="450" class="w-full h-48 object-cover" />@endif
    <div class="p-6">
        <div class="flex flex-wrap gap-2 mb-2">
            @foreach($post->categories as $cat)<span class="text-xs font-extrabold bg-amber-100 text-amber-950 px-2.5 py-1 rounded-lg">{{ $cat->getTranslation('name', app()->getLocale()) }}</span>@endforeach
        </div>
        <h2 class="font-serif text-lg font-extrabold text-slate-950 mb-2"><a href="{{ route('blog.show', $post->slug) }}" class="hover:text-orange-800">{{ $postTitle }}</a></h2>
        <p class="text-slate-700 text-sm mb-4">{{ Str::limit(strip_tags($post->getTranslation('excerpt', app()->getLocale()) ?: $post->getTranslation('content', app()->getLocale())), 120) }}</p>
        <div class="flex justify-between items-center text-xs font-bold text-slate-600">
            @if($post->published_at)
                <time datetime="{{ $post->published_at->toIso8601String() }}">{{ format_date($post->published_at) }}</time>
            @else
                <span></span>
            @endif
            <a href="{{ route('blog.show', $post->slug) }}" aria-label="{{ __('messages.read_more') }}: {{ $postTitle }}" class="text-orange-800 font-extrabold hover:underline">{{ __('messages.read_more') }} <span aria-hidden="true">&rarr;</span></a>
        </div>
    </div>
</article>
