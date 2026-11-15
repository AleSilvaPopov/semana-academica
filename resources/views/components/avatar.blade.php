@props(['nome', 'url' => null, 'tamanho' => 'size-10'])
{{-- Foto do ministrante; sem arquivo, mostra as iniciais do nome. --}}
@if ($url)
    <img src="{{ $url }}" alt="Foto de {{ $nome }}" {{ $attributes->merge(['class' => "$tamanho shrink-0 rounded-full object-cover ring-2 ring-superficie"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$tamanho inline-flex shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700 ring-2 ring-superficie"]) }}
        title="{{ $nome }}">{{ iniciais($nome) }}</span>
@endif
