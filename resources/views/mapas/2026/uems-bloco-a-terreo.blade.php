{{-- Planta: UEMS · Bloco A · térreo (redesenhada em SVG a partir da arte da SIC 2026) --}}
<svg viewBox="0 0 600 570" class="mapa block h-full w-full" role="img" aria-label="Planta do térreo do Bloco A da UEMS, com o Auditório do Bloco A">
    @include('mapas._faixas', ['topo' => 148, 'base' => 447, 'sigla' => 'UEMS', 'nome' => 'Universidade Estadual de Mato Grosso do Sul'])

    {{-- Salas --}}
    <rect x="64" y="148" width="433" height="99" class="sala" />
    <rect x="64" y="345" width="151" height="100" class="sala" />
    <rect x="215" y="345" width="153" height="100" class="sala" />
    <line x1="368" y1="443" x2="497" y2="443" class="linha" />

    {{-- Auditório --}}
    <rect x="498" y="148" width="102" height="299" class="destaque" />
    <text transform="translate(551 298) rotate(-90)" class="txt-sala" font-size="17">AUDITÓRIO BLOCO A</text>

    {{-- Portas --}}
    @include('mapas._porta', ['x1' => 96, 'x2' => 122, 'y' => 247, 'abre' => 'cima'])
    @include('mapas._porta', ['x1' => 96, 'x2' => 122, 'y' => 345, 'abre' => 'baixo'])
    @include('mapas._porta', ['x1' => 230, 'x2' => 256, 'y' => 345, 'abre' => 'baixo'])
    <line x1="498" y1="400" x2="498" y2="441" class="porta" />

    {{-- Setas e título --}}
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(85 298)" />
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(447 298) rotate(90)" />
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(32 398) rotate(-90)" />
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(462 425)" />
    <text x="266" y="298" class="txt" font-size="22">TÉRREO · BLOCO A UEMS</text>
</svg>
