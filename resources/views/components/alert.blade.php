@props(['tipo' => 'info', 'titulo' => null, 'fechavel' => false])
@php
    $estilos = [
        'sucesso' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'check'],
        'erro' => ['bg-red-50 border-red-200 text-red-800', 'warning'],
        'aviso' => ['bg-amber-50 border-amber-200 text-amber-900', 'warning'],
        'info' => ['bg-brand-50 border-brand-200 text-brand-900', 'info'],
    ];
    [$classes, $icone] = $estilos[$tipo] ?? $estilos['info'];
@endphp
<div x-data="{ visivel: true }" x-show="visivel" x-transition.opacity role="alert"
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border px-4 py-3 text-sm $classes"]) }}>
    <x-icon :name="$icone" class="mt-0.5 size-5 shrink-0" />
    <div class="flex-1">
        @if ($titulo)
            <p class="font-semibold">{{ $titulo }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if ($fechavel)
        <button type="button" @click="visivel = false" class="opacity-60 hover:opacity-100" aria-label="Fechar">
            <x-icon name="x" class="size-4" />
        </button>
    @endif
</div>
