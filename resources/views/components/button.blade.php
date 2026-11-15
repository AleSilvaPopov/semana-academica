@props(['variante' => 'primario', 'href' => null, 'type' => 'button', 'tamanho' => 'md', 'icone' => null])
@php
    // Mesmo visual do UI kit (.btn-neon / .btn-outline): fonte display, caixa alta, brilho neon.
    $base = 'inline-flex items-center justify-center gap-1.5 rounded-lg font-display font-bold uppercase tracking-wide transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-offset-fundo disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap';
    $tamanhos = [
        'sm' => 'px-2.5 py-1.5 text-[11px]',
        'md' => 'px-4 py-2 text-xs',
        'lg' => 'px-6 py-3 text-sm',
    ];
    $variantes = [
        'primario' => 'bg-destaque text-white shadow-[0_0_20px_-4px_var(--tema-neon)] hover:bg-neon hover:shadow-[0_0_28px_-2px_var(--tema-neon)] focus-visible:ring-neon',
        'secundario' => 'border border-neon/40 bg-neon/5 text-zinc-950 hover:border-neon hover:shadow-[0_0_16px_-6px_var(--tema-neon)] focus-visible:ring-neon',
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
