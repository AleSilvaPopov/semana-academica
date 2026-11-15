@props(['cor' => 'zinc', 'enum' => null])
{{-- Aceita um PHP Enum (usa ->rotulo() e ->cor()) ou um texto com a cor escolhida. --}}
@php
    if ($enum instanceof \BackedEnum) {
        $cor = $enum->cor();
    }
    $cores = [
        'zinc' => 'bg-zinc-100 text-zinc-700 ring-zinc-500/20',
        'blue' => 'bg-sky-50 text-sky-700 ring-sky-600/20', // "azul" fica azul: a cor de destaque muda a cada ano
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'red' => 'bg-red-50 text-red-700 ring-red-600/20',
        'purple' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'pink' => 'bg-pink-50 text-pink-700 ring-pink-600/20',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset whitespace-nowrap '.($cores[$cor] ?? $cores['zinc'])]) }}>
    {{ $enum instanceof \BackedEnum ? $enum->rotulo() : $slot }}
</span>
