@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.about') }}</h1>

    @forelse($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p>
                <strong>Desde Chipiona</strong> (<code class="font-bold">https://desdechipiona.es</code>) es un proyecto divulgativo y documental de iniciativa privada creado e investigado por <strong>Raúl Caro Pastorino</strong> (<a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer">raupulus.dev</a>).
            </p>
            <p>
                Su objetivo es ofrecer a vecinos, mayores, estudiantes y visitantes un archivo vivo, riguroso, visual y fácil de leer sobre la historia, la naturaleza, las playas, los monumentos y las tradiciones de Chipiona, distinguiendo siempre con honestidad la información contrastada en fuentes de archivo de los mitos y leyendas populares.
            </p>
        </div>
    @endforelse
</div>
@endsection
