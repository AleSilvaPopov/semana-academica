@props(['variante' => 'primario', 'href' => null, 'type' => 'button', 'tamanho' => 'md', 'icone' => null])
@php
    $base = 'inline-flex items-center justify-center gap-1.5 rounded-lg font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-offset-fundo disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap';
    $tamanhos = [
        'sm' => 'px-2.5 py-1.5 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];
    $variantes = [
        'primario' => 'bg-brand-600 text-white shadow-[0_0_18px_-4px_var(--tema-destaque)] hover:brightness-115 hover:shadow-[0_0_24px_-2px_var(--tema-destaque)] focus-visible:ring-brand-500',
        'secundario' => 'border border-zinc-300 bg-superficie text-zinc-800 shadow-xs hover:border-brand-400 hover:bg-zinc-100 focus-visible:ring-zinc-400',
        'perigo' => 'bg-red-600 text-white shadow-xs hover:brightness-115 focus-visible:ring-red-500',
        'sucesso' => 'bg-emerald-600 text-white shadow-xs hover:brightness-115 focus-visible:ring-emerald-500',
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
