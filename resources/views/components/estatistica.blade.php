@props(['titulo', 'valor', 'icone' => 'chart', 'detalhe' => null, 'cor' => 'brand'])
@php
    $cores = [
        'brand' => 'bg-brand-50 text-brand-700',
        'green' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'red' => 'bg-red-50 text-red-700',
        'zinc' => 'bg-zinc-100 text-zinc-700',
    ];
@endphp
<div {{ $attributes->merge(['class' => 'cartao flex items-start gap-4 p-5']) }}>
    <div class="rounded-lg p-2.5 {{ $cores[$cor] ?? $cores['brand'] }}"><x-icon :name="$icone" class="size-6" /></div>
    <div class="min-w-0">
        <p class="text-sm text-zinc-500">{{ $titulo }}</p>
        <p class="mt-0.5 text-2xl font-semibold tracking-tight text-zinc-900 tabular-nums">{{ $valor }}</p>
        @if ($detalhe)
            <p class="mt-0.5 text-xs text-zinc-500">{{ $detalhe }}</p>
        @endif
    </div>
</div>
