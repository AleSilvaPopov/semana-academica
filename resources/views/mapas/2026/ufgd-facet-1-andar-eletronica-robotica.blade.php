{{-- Planta: UFGD · FACET · primeiro andar (Lab de Eletrônica e Lab de Robótica) — redesenhada em SVG a partir da arte da SIC 2026 --}}
<svg viewBox="0 0 600 570" class="mapa block h-full w-full" role="img" aria-label="Planta do primeiro andar da FACET, UFGD, com o Lab de Eletrônica e o Lab de Robótica">
    @include('mapas._faixas', ['topo' => 148, 'base' => 442, 'sigla' => 'UFGD', 'nome' => 'Universidade Federal da Grande Dourados'])

    {{-- Linha de cima --}}
    <rect x="-2" y="148" width="38" height="70" class="wc" />
    <rect x="36" y="148" width="60" height="70" class="wc" />
    <rect x="96" y="148" width="60" height="70" class="wc" />
    <rect x="156" y="148" width="150" height="98" class="destaque" />
    <rect x="306" y="148" width="150" height="98" class="sala" />
    <rect x="456" y="148" width="146" height="98" class="destaque" />
    <text transform="translate(17 183) rotate(-90)" class="txt-mini">ELEVADOR</text>
    <use href="#mapa-homem" x="52" y="160" width="20" height="44" />
    <use href="#mapa-mulher" x="114" y="160" width="20" height="44" />
    <text x="228" y="180" class="txt-sala" font-size="15">LAB DE</text>
    <text x="228" y="206" class="txt-sala" font-size="15">ELETRÔNICA</text>
    <text x="526" y="183" class="txt-sala" font-size="15">LAB DE</text>
    <text x="526" y="209" class="txt-sala" font-size="15">ROBÓTICA</text>
    @include('mapas._porta', ['x1' => 9, 'x2' => 23, 'y' => 218])
    @include('mapas._porta', ['x1' => 56, 'x2' => 70, 'y' => 218])
    @include('mapas._porta', ['x1' => 124, 'x2' => 139, 'y' => 218])
    @include('mapas._porta', ['x1' => 262, 'x2' => 292, 'y' => 246, 'abre' => 'cima'])
    @include('mapas._porta', ['x1' => 418, 'x2' => 446, 'y' => 246, 'abre' => 'cima'])
    @include('mapas._porta', ['x1' => 562, 'x2' => 592, 'y' => 246, 'abre' => 'cima'])
    @foreach ([[62, 238], [134, 238], [277, 263], [583, 263]] as [$x, $y])
        <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate({{ $x }} {{ $y }}) rotate(-90) scale(.55)" />
    @endforeach

    {{-- Linha de baixo --}}
    <rect x="-2" y="344" width="37" height="72" class="wc" />
    @for ($y = 350; $y <= 408; $y += 7)
        <line x1="2" y1="{{ $y }}" x2="31" y2="{{ $y }}" class="escada" />
    @endfor
    <line x1="9" y1="408" x2="9" y2="350" class="escada" /><path d="M6 354l3-5 3 5" class="escada" />
    <line x1="35" y1="344" x2="158" y2="344" class="parede" />
    <rect x="158" y="344" width="150" height="98" class="sala" />
    <rect x="308" y="344" width="148" height="98" class="sala" />
    <rect x="456" y="344" width="146" height="98" class="sala" />
    @include('mapas._porta', ['x1' => 262, 'x2' => 288, 'y' => 344, 'abre' => 'baixo'])
    @include('mapas._porta', ['x1' => 410, 'x2' => 434, 'y' => 344, 'abre' => 'baixo'])
    @include('mapas._porta', ['x1' => 564, 'x2' => 590, 'y' => 344, 'abre' => 'baixo'])

    {{-- Título e direção --}}
    <text x="100" y="288" class="txt" font-size="18">PRIMEIRO</text>
    <text x="100" y="309" class="txt" font-size="18">ANDAR · FACET</text>
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(236 298) scale(1.25)" />
</svg>
