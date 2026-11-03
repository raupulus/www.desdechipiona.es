@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            ⚖️ Transparencia, Autoría y Licencia Libre
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            Autoría, Iniciativa Privada, Aviso Legal y Licencia AGPLv3
        </h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            Información clara sobre quién investiga y redacta <strong>Desde Chipiona</strong>, la distinción entre hechos documentados y leyendas, y las condiciones legales del portal.
        </p>
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    {{-- 1. Autoría e Iniciativa Privada --}}
    <section class="rounded-2xl border-2 border-orange-700 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            1. Autoría e Iniciativa Privada: Raúl Caro Pastorino
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            <strong>Desde Chipiona</strong> (<code class="font-bold text-orange-900">https://desdechipiona.es</code>) es una obra divulgativa y documental de <strong>iniciativa estrictamente privada e independiente</strong>. No depende de ningún organismo público ni partido político; es fruto del trabajo personal de investigación histórica, documental y técnica de su autor:
        </p>
        <div class="rounded-xl border border-slate-200 bg-stone-50 p-5 space-y-2 text-sm text-slate-900">
            <p><strong>Autor e investigador titular:</strong> Raúl Caro Pastorino</p>
            <p><strong>Sitio web personal y porfolio profesional:</strong> <a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer" class="font-extrabold text-blue-900 underline">https://raupulus.dev</a></p>
            <p><strong>Correo público del autor:</strong> <a href="mailto:public@raupulus.dev" class="font-extrabold text-blue-900 underline">public@raupulus.dev</a></p>
            <p><strong>Correo de contacto del portal:</strong> <a href="mailto:info@desdechipiona.es" class="font-extrabold text-blue-900 underline">info@desdechipiona.es</a></p>
            <p><strong>Envíos automáticos del sistema:</strong> <code class="font-mono">no-reply@desdechipiona.es</code></p>
            <p><strong>Canal oficial de difusión:</strong> <a href="https://t.me/desdechipiona" target="_blank" rel="noopener noreferrer" class="font-extrabold text-blue-900 underline">https://t.me/desdechipiona</a></p>
        </div>
    </section>

    {{-- 2. Metodología: Hechos contrastados vs. mitos y leyendas --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            2. Advertencia Metodológica: Información Contrastada frente a Mitos y Leyendas
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            La historia de un pueblo como Chipiona combina legajos de archivo, hallazgos arqueológicos, memorias orales y leyendas populares transmitidas de generación en generación. En esta plataforma mantenemos una separación rigurosa y visible en cada página:
        </p>
        <ul class="space-y-3 text-sm text-slate-900 list-disc pl-6">
            <li><strong>Información Contrastada / Consolidada:</strong> Respaldada por fuentes primarias de archivo (Archivo Histórico Nacional, Archivo Ducal de Medina Sidonia, Autoridad Portuaria, BOE, hemeroteca histórica) o publicaciones académicas, identificadas con su código <code class="font-mono font-bold">[F-XXXX]</code>.</li>
            <li><strong>Mito, Leyenda o Tradición Oral:</strong> Relatos populares de gran valor cultural e identitario (como el ciclo legendario africano de la Virgen de Regla o dichos antiguos), que se presentan expresamente como tradición y nunca como hecho empírico cerrado.</li>
            <li><strong>Hipótesis o Dato Discutido / Sin Contrastar:</strong> Cuestiones en las que las fuentes históricas discrepan o donde aún faltan excavaciones o documentos concluyentes.</li>
        </ul>
    </section>

    {{-- 3. Licencia del Software y Contenidos --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            3. Licencia del Proyecto: GNU AGPLv3
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            El código fuente de la plataforma <strong>Desde Chipiona</strong> se publica bajo la licencia libre <strong>GNU Affero General Public License v3.0 (AGPL-3.0-or-later)</strong>, garantizando que cualquier mejora o despliegue en red del software permanezca abierto a la comunidad.
        </p>
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('privacy') }}" class="rounded-xl bg-slate-950 px-5 py-2.5 text-xs font-extrabold text-white hover:bg-orange-800">
                Ver Política de Privacidad
            </a>
            <a href="{{ route('cookies') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-slate-200">
                Ver Política de Cookies
            </a>
            <a href="{{ route('contact') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-slate-200">
                Contactar (info@desdechipiona.es)
            </a>
        </div>
    </section>
</div>
@endsection
