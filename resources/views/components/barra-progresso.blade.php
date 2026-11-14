@props(['valor' => 0, 'total' => 1])
@php
    $pct = $total > 0 ? min(100, round($valor / $total * 100)) : 0;
    $cor = $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-brand-500');
@endphp
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="h-2 w-full min-w-16 overflow-hidden rounded-full bg-zinc-100">
        <div class="h-full rounded-full {{ $cor }}" style="width: {{ $pct }}%"></div>
    </div>
    <span class="w-10 text-right text-xs text-zinc-500 tabular-nums">{{ $pct }}%</span>
</div>
