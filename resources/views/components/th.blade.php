@props(['coluna' => null, 'class' => ''])
{{-- Cabeçalho de coluna; com "coluna" vira link de ordenação (?ordem=coluna&direcao=asc|desc). --}}
@php
    $ordemAtual = request('ordem');
    $direcaoAtual = request('direcao', 'asc') === 'desc' ? 'desc' : 'asc';
    $ativa = $coluna && $ordemAtual === $coluna;
    $proxima = $ativa && $direcaoAtual === 'asc' ? 'desc' : 'asc';
@endphp
<th {{ $attributes->merge(['class' => $class]) }}>
    @if ($coluna)
        <a href="{{ request()->fullUrlWithQuery(['ordem' => $coluna, 'direcao' => $proxima, 'page' => null]) }}"
            class="inline-flex items-center gap-1 hover:text-zinc-800 {{ $ativa ? 'text-zinc-900' : '' }}">
            {{ $slot }}
            @if ($ativa)
                <span aria-hidden="true">{{ $direcaoAtual === 'asc' ? '▲' : '▼' }}</span>
            @else
                <x-icon name="chevron-up-down" class="size-3.5 opacity-50" />
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>
