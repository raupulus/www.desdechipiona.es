@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-24 text-center">
    <h1 class="font-serif text-6xl font-extrabold text-rose-800">500</h1>
    <p class="mt-4 text-xl font-bold text-slate-800">{{ __('messages.error_500_title') }}</p>
    <a href="{{ route('home') }}" class="mt-8 inline-block bg-slate-950 text-white font-extrabold px-6 py-3 rounded-xl hover:bg-orange-800 transition">{{ __('messages.back_home') }}</a>
</div>
@endsection
