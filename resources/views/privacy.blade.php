@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.privacy') }}</h1>

    @forelse ($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p class="text-sm font-bold text-slate-700">{{ __('messages.privacy_default_text') }}</p>
            <h2>1. Responsable del Tratamiento</h2>
            <p>
                El responsable del tratamiento de los datos recogidos en <strong>Desde Chipiona</strong> (<code class="font-bold">https://desdechipiona.es</code>) es <strong>Raúl Caro Pastorino</strong> (<a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer">raupulus.dev</a>), con correo de contacto <a href="mailto:info@desdechipiona.es">info@desdechipiona.es</a> y correo público del autor <a href="mailto:public@raupulus.dev">public@raupulus.dev</a>.
            </p>
            <h2>2. Sin cuentas de usuarios registradas para visitantes</h2>
            <p>
                Esta plataforma está abierta al público sin necesidad de crear cuentas de usuario ni almacenar contraseñas para los lectores. Cuando un visitante utiliza la identificación opcional con Facebook o Google para publicar un comentario, proponer una corrección o enviar un refrán, únicamente se registra su nombre visible y el identificador básico de sesión para acreditar la autoría del aporte y prevenir el spam junto con <strong>Cloudflare Turnstile</strong>.
            </p>
            <h2>3. Ejercicio de Derechos</h2>
            <p>
                Cualquier persona puede solicitar el acceso, rectificación o supresión de sus comentarios, sugerencias o propuestas escribiendo directamente a <a href="mailto:info@desdechipiona.es">info@desdechipiona.es</a>.
            </p>
        </div>
    @endforelse
</div>
@endsection
