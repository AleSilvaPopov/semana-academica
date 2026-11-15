{{--
    Porta numa parede horizontal: vão branco entre $x1 e $x2 na altura $y.
    $abre: 'cima' ou 'baixo' (lado para onde a folha abre; desenha o arco). null = sem arco.
--}}
@php
    $abre = $abre ?? null;
    $r = $x2 - $x1;
    $s = $abre === 'cima' ? -1 : 1;
@endphp
<line x1="{{ $x1 - 7 }}" y1="{{ $y }}" x2="{{ $x1 }}" y2="{{ $y }}" class="batente" />
<line x1="{{ $x2 }}" y1="{{ $y }}" x2="{{ $x2 + 7 }}" y2="{{ $y }}" class="batente" />
<line x1="{{ $x1 }}" y1="{{ $y }}" x2="{{ $x2 }}" y2="{{ $y }}" class="porta" />
@if ($abre)
    <path d="M{{ $x2 }} {{ $y }}A{{ $r }} {{ $r }} 0 0 {{ $s < 0 ? 0 : 1 }} {{ $x1 }} {{ $y + $s * $r }}L{{ $x1 }} {{ $y }}" class="arco" />
@endif
