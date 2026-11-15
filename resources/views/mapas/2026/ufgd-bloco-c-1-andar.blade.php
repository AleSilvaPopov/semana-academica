{{-- Planta: UFGD · Bloco C · primeiro andar (redesenhada em SVG a partir da arte da SIC 2026) --}}
<svg viewBox="0 0 600 570" class="mapa block h-full w-full" role="img" aria-label="Planta do primeiro andar do Bloco C da UFGD, com a Sala C-208">
    @include('mapas._faixas', ['topo' => 137, 'base' => 458, 'sigla' => 'UFGD', 'nome' => 'Universidade Federal da Grande Dourados'])

    {{-- Linha de cima --}}
    <rect x="-2" y="137" width="40" height="115" class="sala" />
    <rect x="38" y="137" width="71" height="85" class="wc" />
    <rect x="109" y="137" width="71" height="85" class="wc" />
    <rect x="180" y="137" width="43" height="85" class="wc" />
    <rect x="223" y="137" width="177" height="115" class="sala" />
    <rect x="400" y="137" width="173" height="115" class="sala" />
    <rect x="573" y="137" width="29" height="115" class="sala" />
    <use href="#mapa-homem" x="62" y="152" width="20" height="44" />
    <use href="#mapa-mulher" x="136" y="152" width="20" height="44" />
    <text transform="translate(202 180) rotate(-90)" class="txt-mini">ELEVADOR</text>
    @include('mapas._porta', ['x1' => 62, 'x2' => 84, 'y' => 222])
    @include('mapas._porta', ['x1' => 135, 'x2' => 157, 'y' => 222])
    @include('mapas._porta', ['x1' => 194, 'x2' => 212, 'y' => 222])
    @include('mapas._porta', ['x1' => 4, 'x2' => 28, 'y' => 252, 'abre' => 'cima'])
    @include('mapas._porta', ['x1' => 350, 'x2' => 376, 'y' => 252, 'abre' => 'cima'])
    @include('mapas._porta', ['x1' => 529, 'x2' => 555, 'y' => 252, 'abre' => 'cima'])
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(73 240) rotate(-90) scale(.55)" />
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(147 240) rotate(-90) scale(.55)" />

    {{-- Linha de baixo --}}
    <rect x="-2" y="341" width="40" height="117" class="sala" />
    <path d="M38 342H62C80 374 140 374 158 342H180" class="parede" />
    <rect x="180" y="341" width="43" height="87" class="wc" />
    @for ($y = 348; $y <= 404; $y += 7)
        <line x1="184" y1="{{ $y }}" x2="219" y2="{{ $y }}" class="escada" />
    @endfor
    <line x1="211" y1="404" x2="211" y2="350" class="escada" /><path d="M208 354l3-5 3 5" class="escada" />
    <rect x="223" y="341" width="176" height="117" class="sala" />
    <rect x="399" y="341" width="175" height="117" class="destaque" />
    <rect x="574" y="341" width="28" height="117" class="sala" />
    <text x="487" y="400" class="txt-sala" font-size="16">SALA C-208</text>
    @include('mapas._porta', ['x1' => 4, 'x2' => 26, 'y' => 341, 'abre' => 'baixo'])
    @include('mapas._porta', ['x1' => 344, 'x2' => 370, 'y' => 341, 'abre' => 'baixo'])
    @include('mapas._porta', ['x1' => 520, 'x2' => 553, 'y' => 341])
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(536 326) rotate(90) scale(.55)" />

    {{-- Título e direção --}}
    <text x="106" y="286" class="txt" font-size="18">PRIMEIRO ANDAR</text>
    <text x="106" y="309" class="txt" font-size="18">BLOCO C</text>
    <use href="#mapa-seta" x="-15" y="-12" width="30" height="24" transform="translate(243 298) scale(1.25)" />
</svg>
