@props(['variante' => 'primario', 'href' => null, 'type' => 'button', 'tamanho' => 'md', 'icone' => null])
@php
    $base = 'inline-flex items-center justify-center gap-1.5 rounded-lg font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap';
    $tamanhos = [
        'sm' => 'px-2.5 py-1.5 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];
    $variantes = [
        'primario' => 'bg-brand-600 text-white shadow-xs hover:bg-brand-700 focus-visible:ring-brand-500',
        'secundario' => 'border border-zinc-300 bg-white text-zinc-700 shadow-xs hover:bg-zinc-50 focus-visible:ring-zinc-400',
        'perigo' => 'bg-red-600 text-white shadow-xs hover:bg-red-700 focus-visible:ring-red-500',
        'sucesso' => 'bg-emerald-600 text-white shadow-xs hover:bg-emerald-700 focus-visible:ring-emerald-500',
        'fantasma' => 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 focus-visible:ring-zinc-400',
    ];
    $classes = $base.' '.($tamanhos[$tamanho] ?? $tamanhos['md']).' '.($variantes[$variante] ?? $variantes['primario']);
    $classeIcone = $tamanho === 'sm' ? 'size-4' : 'size-[1.1rem]';
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icone)<x-icon :name="$icone" :class="$classeIcone" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icone)<x-icon :name="$icone" :class="$classeIcone" />@endif
        {{ $slot }}
    </button>
@endif
