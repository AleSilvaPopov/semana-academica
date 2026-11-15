{{--
    Marca "SIC//2026": ícone + sigla + barras neon + ano.
    Uso: @include('identidade.marca', ['icone' => true, 'tamanho' => 'text-lg'])
--}}
@php
    $icone = $icone ?? true;
    $tamanho = $tamanho ?? 'text-lg';
@endphp
<span class="inline-flex items-center gap-2.5">
    @if ($icone)
        @include('identidade.logo', ['classe' => 'size-7'])
    @endif
    <span class="font-display {{ $tamanho }} leading-none font-bold tracking-tight text-zinc-950">{{ config('identidade.sigla') }}<span class="text-neon">//</span>{{ config('identidade.ano') }}</span>
</span>
