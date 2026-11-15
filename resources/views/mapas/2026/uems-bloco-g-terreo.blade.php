{{-- Planta: UEMS · Bloco G · térreo (redesenhada em SVG a partir da arte da SIC 2026) --}}
<svg viewBox="0 0 600 570" class="mapa block h-full w-full" role="img" aria-label="Planta do térreo do Bloco G da UEMS, com o Auditório do Bloco G e os laboratórios I, II e III">
    @include('mapas._faixas', ['topo' => 124, 'base' => 469, 'sigla' => 'UEMS', 'nome' => 'Universidade Estadual de Mato Grosso do Sul'])

    {{-- Linha de cima --}}
    <rect x="-2" y="124" width="90" height="98" class="sala" />
    <rect x="88" y="124" width="88" height="98" class="sala" />
    <rect x="176" y="124" width="238" height="98" class="destaque" />
    <rect x="414" y="124" width="89" height="98" class="sala" />
    <rect x="503" y="124" width="99" height="98" class="sala" />
    <text x="295" y="174" class="txt-sala" font-size="16">AUDITÓRIO BLOCO G</text>
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(295 241) rotate(-90) scale(.55)" />

    {{-- Meio --}}
    <rect x="-2" y="250" width="201" height="94" class="sala" />
    <rect x="400" y="250" width="202" height="94" class="sala" />
    @foreach ([200, 215, 230, 245] as $x)<rect x="{{ $x }}" y="250" width="13" height="37" class="wc" />@endforeach
    @foreach ([340, 355, 370, 385] as $x)<rect x="{{ $x }}" y="276" width="12" height="36" class="wc" />@endforeach
    <rect x="201" y="311" width="18" height="19" class="wc" /><rect x="205" y="315" width="10" height="11" class="wc" />
    <text x="303" y="332" class="txt" font-size="17">TÉRREO</text>
    <text x="303" y="352" class="txt" font-size="17">BLOCO G</text>
    <text x="303" y="372" class="txt" font-size="17">UEMS</text>

    {{-- Linha de baixo --}}
    <rect x="-2" y="374" width="90" height="95" class="destaque" />
    <rect x="88" y="374" width="89" height="95" class="destaque" />
    <rect x="177" y="374" width="46" height="95" class="wc" />
    <rect x="387" y="374" width="47" height="95" class="wc" />
    <rect x="434" y="374" width="118" height="95" class="destaque" />
    <rect x="552" y="374" width="50" height="95" class="sala" />
    <text x="44" y="420" class="txt-sala" font-size="15">LAB I</text>
    <text x="132" y="420" class="txt-sala" font-size="15">LAB II</text>
    <text x="493" y="421" class="txt-sala" font-size="15">LAB III</text>
    <use href="#mapa-mulher" x="190" y="399" width="20" height="44" />
    <use href="#mapa-homem" x="400" y="399" width="20" height="44" />
    @include('mapas._porta', ['x1' => 42, 'x2' => 73, 'y' => 374])
    @include('mapas._porta', ['x1' => 134, 'x2' => 165, 'y' => 374])
    @include('mapas._porta', ['x1' => 504, 'x2' => 536, 'y' => 374])
    @foreach ([58, 150, 201, 410, 519] as $x)
        <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate({{ $x }} 357) rotate(90) scale(.55)" />
    @endforeach
</svg>
