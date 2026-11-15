{{-- Mapa do campus (blocos) — redesenhado em SVG a partir da arte da SIC 2026 --}}
<svg viewBox="0 0 600 570" class="mapa mapa-campus block h-full w-full" role="img"
    aria-label="Mapa do campus com a FACET, o Bloco Multiuso e o Bloco C da UFGD e o Bloco G da UEMS"
    style="--fundo-mapa:#2e2e30; --fundo-claro:#57534d; --area-borda:#8d7a55; --borda:#4ea8e0">
    <rect width="600" height="570" fill="var(--fundo-mapa)" />

    {{-- Áreas claras --}}
    <path d="M0 0H400L300 48L105 125L0 100Z" class="area area-borda" />
    <path d="M458 452L600 428V570H480Z" class="area area-borda" />

    {{-- Ruas internas (linha dupla) --}}
    @foreach ([
        'M5 243L200 200', 'M226 191L358 166', 'M358 40L360 166', 'M360 148L535 128',
        'M362 72L484 58L500 45L600 38', 'M524 60L528 228L505 234', 'M440 150L455 182L460 330',
        'M490 148L468 182L462 205', 'M536 168L600 160', 'M300 105L356 98', 'M86 197L89 224',
        'M300 358L362 348L372 340L390 337', 'M472 285L484 412', 'M412 478L418 512',
        'M428 476L436 522', 'M480 562L600 546', 'M2 522L60 512',
    ] as $d)
        @include('mapas._via', ['d' => $d])
    @endforeach

    {{-- Estradas --}}
    @include('mapas._estradas', ['vias' => [
        ['M4 238L30 400L64 575', 22],
        ['M-5 516L300 466L605 422', 26],
        ['M193 58L212 192L261 474', 22],
        ['M175 42a17 17 0 1 0 34 0a17 17 0 1 0 -34 0', 12], {{-- rotatória --}}
    ]])
    <path d="M194 66L212 188" fill="none" stroke="var(--borda)" stroke-width="1.2" />
    <path d="M452 430L566 413" fill="none" stroke="var(--borda)" stroke-width="5" stroke-linecap="round" />
    <path d="M452 430L566 413" fill="none" stroke="var(--fundo-mapa)" stroke-width="2.6" stroke-linecap="round" />

    {{-- Prédios --}}
    @foreach ([
        '237 118 300 110 300 146 242 156', '17 258 184 227 187 250 22 283', '120 292 208 281 212 318 126 328',
        '262 262 300 256 302 284 266 290', '268 298 318 291 322 343 276 351', '30 321 52 318 55 340 32 344',
        '42 380 72 374 75 397 46 404', '132 370 209 360 212 384 137 391', '55 418 115 410 120 438 60 446',
        '150 427 214 418 219 439 154 447', '122 553 141 550 144 572 125 572', '153 555 173 552 176 572 156 572',
        '-2 540 15 538 17 567 -2 569', '553 47 602 42 602 78 557 80', '588 108 602 107 602 140 592 142',
        '395 236 472 226 478 280 400 288', '322 254 372 248 374 276 324 281', '322 292 372 282 378 322 328 330',
        '383 318 428 310 432 338 386 345', '428 312 458 307 460 328 431 332', '368 358 415 350 420 372 372 378',
        '300 362 330 358 333 398 303 402', '503 372 515 370 518 385 506 387', '342 535 398 525 405 558 348 566',
        '392 290 420 287 422 308 394 310', '565 250 602 246 602 285 568 288',
    ] as $p)
        <polygon points="{{ $p }}" class="predio" />
    @endforeach
    <line x1="43" y1="319" x2="45" y2="342" class="predio" />
    <line x1="96" y1="412" x2="101" y2="440" class="predio" />

    {{-- Prédios do evento --}}
    <path d="M12 162L64 152L66 148L84 146L84 141L135 132L137 174L18 193Z" class="evento" />
    <path d="M65 152L68 185M85 147L88 182M67 166L86 163" class="divisa" />
    <path d="M365 38L480 24L485 50L370 68Z" class="evento" />
    <path d="M370 92L485 74L490 110L446 116L446 124L430 126L429 119L375 127Z" class="evento" />
    <path d="M228 533L262 528L265 523L318 513L326 572L236 572Z" class="evento" />

    {{-- Pinos e nomes --}}
    @foreach ([[35, 120], [381, 12], [386, 62], [256, 506]] as [$x, $y])
        <use href="#mapa-pino" x="-9" y="-9" width="18" height="40" transform="translate({{ $x }} {{ $y }})" />
    @endforeach
    @include('mapas._rotulo', ['x' => 92, 'y' => 128, 'texto' => 'FACET · UFGD'])
    @include('mapas._rotulo', ['x' => 478, 'y' => 37, 'texto' => 'BLOCO MULTIUSO · UFGD'])
    @include('mapas._rotulo', ['x' => 462, 'y' => 96, 'texto' => 'BLOCO C · UFGD'])
    @include('mapas._rotulo', ['x' => 300, 'y' => 548, 'texto' => 'BLOCO G · UEMS'])
</svg>
