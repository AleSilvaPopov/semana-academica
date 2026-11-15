{{-- Mapa do campus (auditórios) — redesenhado em SVG a partir da arte da SIC 2026 --}}
<svg viewBox="0 0 600 570" class="mapa mapa-campus block h-full w-full" role="img"
    aria-label="Mapa do campus com o Auditório da UFGD, o Bloco A e o Auditório Central da UEMS"
    style="--fundo-mapa:#2b2b2c; --fundo-claro:#505050; --borda:#d2483a; --evento:#d81e00; --evento-borda:#ff6a4a">
    <rect width="600" height="570" class="area" />

    {{-- Áreas do campus (escuras) --}}
    <path d="M0 110L212 56L240 62L262 210L265 290L248 425L0 452Z" fill="var(--fundo-mapa)" />
    <path d="M125 343L225 333L242 425L135 430Z" class="area" />
    <path d="M20 500L250 466L262 572L25 572Z" fill="var(--fundo-mapa)" />

    {{-- Ruas internas (linha dupla) --}}
    @foreach ([
        'M114 128L195 112', 'M183 114L186 140', 'M114 128L122 250L130 290L150 330', 'M0 200L130 182L262 163',
        'M0 265L130 243L210 233', 'M40 185L44 222', 'M195 170L198 205', 'M60 290L98 345L104 400L100 445',
        'M118 290L135 450',
    ] as $d)
        @include('mapas._via', ['d' => $d])
    @endforeach
    @include('mapas._via', ['d' => 'M465 78L262 120L272 228L305 268', 'clara' => true])

    {{-- Estradas e trevo --}}
    @include('mapas._estradas', ['vias' => [
        ['M-5 412L78 472', 12],
        ['M-5 504L240 472L282 470L300 490L340 522', 13],
        ['M-5 483L300 428L360 412L415 398', 19],
        ['M494 366L530 402L605 460', 12],
        ['M296 578L380 457L470 335L530 255L605 160', 22],
        ['M336 578L420 466L494 365L522 318L548 268L557 226', 20],
    ]])

    {{-- Prédios --}}
    @foreach ([
        '125 93 165 86 166 89 185 85 184 80 228 71 232 100 130 118', '137 151 242 129 245 158 142 180',
        '148 203 208 195 210 190 250 182 253 228 155 246', '-2 163 23 159 26 183 -2 188', '30 164 83 152 88 183 33 193',
        '-2 218 93 203 98 230 -2 250', '102 262 120 259 121 266 103 269', '217 278 255 271 258 295 220 300',
        '52 256 98 248 100 262 54 268', '-2 282 50 272 52 290 -2 300',
        '35 368 83 357 86 382 95 395 93 410 100 420 90 430 80 422 65 430 60 422 45 432 28 415 35 405 25 395 38 385',
    ] as $p)
        <polygon points="{{ $p }}" class="predio" />
    @endforeach
    {{-- Prédio na área clara (só contorno) --}}
    <polygon points="342 186 380 180 380 172 388 170 390 178 423 173 426 195 345 210" class="predio" style="fill: var(--fundo-claro)" />
    <path d="M362 183L364 207M385 178L387 202" class="predio" fill="none" />
    <line x1="237" y1="274" x2="240" y2="297" class="predio" />
    <rect x="-8" y="-8" width="16" height="16" transform="translate(178 295) rotate(40)" class="predio" />

    {{-- Prédios do evento --}}
    <path d="M505 30L545 22L555 25L575 25L580 40L590 55L595 82L560 90L545 92L540 105L533 105L532 95L500 102L495 85L478 80L483 65L470 60L495 50Z" class="evento" />
    <path d="M20 528L78 518L80 522L115 513L120 543L25 558Z" class="evento" />
    <path d="M165 530L230 517L238 572L172 572Z" class="evento" />

    {{-- Pinos e nomes --}}
    @foreach ([[542, 12], [68, 505], [212, 500]] as [$x, $y])
        <use href="#mapa-pino" x="-9" y="-9" width="18" height="40" transform="translate({{ $x }} {{ $y }})" />
    @endforeach
    @include('mapas._rotulo', ['x' => 530, 'y' => 62, 'texto' => 'AUDITÓRIO UFGD'])
    @include('mapas._rotulo', ['x' => 74, 'y' => 548, 'texto' => 'BLOCO A · UEMS'])
    @include('mapas._rotulo', ['x' => 216, 'y' => 546, 'texto' => "AUDITÓRIO CENTRAL\nUEMS"])
</svg>
