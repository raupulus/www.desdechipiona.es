@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.cookies') }}</h1>

    @forelse ($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p class="text-sm font-bold text-slate-700">{{ __('messages.cookies_default_text') }}</p>
            <h2>1. Uso exclusivo de cookies técnicas necesarias</h2>
            <p>
                En <strong>Desde Chipiona</strong> (<code class="font-bold">https://desdechipiona.es</code>) no utilizamos cookies publicitarias ni perfiles de seguimiento comercial de terceros. Únicamente empleamos almacenamiento local y cookies técnicas estrictamente necesarias para el funcionamiento del sitio:
            </p>
            <ul>
                <li><strong>Sesión y seguridad CSRF de Laravel:</strong> Imprescindibles para el envío seguro de formularios de participación y protección contra ataques.</li>
                <li><strong>Cloudflare Turnstile:</strong> Verificación técnica anti-bots en todos los envíos de formularios sin rastreo publicitario.</li>
                <li><strong>Preferencias de lectura accesible (<code class="font-mono">chipiona_font_scale</code>):</strong> Guarda en tu navegador el tamaño de letra elegido (<code class="font-mono">A / A+ / A++</code>).</li>
            </ul>
        </div>
    @endforelse
</div>
@endsection
