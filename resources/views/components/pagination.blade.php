{{-- Componente de paginación personalizado con Tailwind --}}
@props(['paginator'])

@if ($paginator->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $paginator->links() }}
    </div>
@endif
