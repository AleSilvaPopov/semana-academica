{{-- Planta: UFGD · FACET · primeiro andar (Lab I, Lab de Redes, Lab II) — redesenhada em SVG a partir da arte da SIC 2026 --}}
<svg viewBox="0 0 600 570" class="mapa block h-full w-full" role="img" aria-label="Planta do primeiro andar da FACET, UFGD, com o Lab I, o Lab de Redes e o Lab II">
    @include('mapas._faixas', ['topo' => 146, 'base' => 446, 'sigla' => 'UFGD', 'nome' => 'Universidade Federal da Grande Dourados'])

    {{-- Linha de cima --}}
    <rect x="-2" y="146" width="149" height="99" class="sala" />
    <rect x="147" y="146" width="149" height="99" class="sala" />
    <rect x="296" y="146" width="149" height="99" class="sala" />
    <rect x="445" y="146" width="36" height="72" class="wc" />
    <rect x="481" y="146" width="60" height="72" class="wc" />
    <rect x="541" y="146" width="61" height="72" class="wc" />
    <text transform="translate(463 182) rotate(-90)" class="txt-mini">ELEVADOR</text>
    <use href="#mapa-homem" x="501" y="158" width="20" height="44" />
    <use href="#mapa-mulher" x="565" y="158" width="20" height="44" />
    @include('mapas._porta', ['x1' => 118, 'x2' => 132, 'y' => 245])
    @include('mapas._porta', ['x1' => 260, 'x2' => 274, 'y' => 245])
    @include('mapas._porta', ['x1' => 410, 'x2' => 424, 'y' => 245])
    @include('mapas._porta', ['x1' => 456, 'x2' => 470, 'y' => 218])
    @include('mapas._porta', ['x1' => 502, 'x2' => 516, 'y' => 218])
    @include('mapas._porta', ['x1' => 569, 'x2' => 583, 'y' => 218])
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(506 237) rotate(-90) scale(.55)" />
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(578 237) rotate(-90) scale(.55)" />

    {{-- Linha de baixo --}}
    <rect x="-2" y="346" width="128" height="100" class="destaque" />
    <rect x="126" y="346" width="145" height="100" class="destaque" />
    <rect x="305" y="346" width="141" height="100" class="destaque" />
    <text x="60" y="404" class="txt-sala" font-size="15">LAB I</text>
    <text x="198" y="404" class="txt-sala" font-size="15">LAB DE REDES</text>
    <text x="375" y="404" class="txt-sala" font-size="15">LAB II</text>
    <line x1="271" y1="356" x2="271" y2="372" class="porta" />
    <line x1="305" y1="356" x2="305" y2="372" class="porta" />
    <use href="#mapa-seta-dupla" x="-15" y="-7" width="30" height="14" transform="translate(288 364) scale(.85)" />
    @include('mapas._porta', ['x1' => 284, 'x2' => 298, 'y' => 385])
    <rect x="446" y="346" width="36" height="72" class="wc" />
    @for ($y = 352; $y <= 410; $y += 7)
        <line x1="450" y1="{{ $y }}" x2="478" y2="{{ $y }}" class="escada" />
    @endfor
    <line x1="456" y1="410" x2="456" y2="352" class="escada" /><path d="M453 356l3-5 3 5" class="escada" />
    <line x1="482" y1="346" x2="600" y2="346" class="parede" />
    @include('mapas._porta', ['x1' => 103, 'x2' => 118, 'y' => 347])
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(110 331) rotate(90) scale(.55)" />

    {{-- Título e direção --}}
    <text x="498" y="286" class="txt" font-size="18">PRIMEIRO</text>
    <text x="498" y="307" class="txt" font-size="18">ANDAR · FACET</text>
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(372 306) rotate(180) scale(1.25)" />
</svg>
