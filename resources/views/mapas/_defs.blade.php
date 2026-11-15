{{--
    Definições compartilhadas pelos mapas em SVG (gradientes, ondas, setas, pictogramas).
    Incluir UMA vez por página, antes dos mapas. Fica visível (0×0) de propósito:
    gradientes dentro de um <svg> com display:none não renderizam em alguns navegadores.
--}}
<svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="mapa-faixa-topo" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#ff3a0a" />
            <stop offset=".55" stop-color="#c41000" />
            <stop offset="1" stop-color="#050505" />
        </linearGradient>
        <linearGradient id="mapa-faixa-base" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#050505" />
            <stop offset=".45" stop-color="#c41000" />
            <stop offset="1" stop-color="#ff3a0a" />
        </linearGradient>
        <pattern id="mapa-ondas" width="120" height="14" patternUnits="userSpaceOnUse">
            <path d="M0 7C20 0 40 0 60 7S100 14 120 7" fill="none" stroke="#000" stroke-opacity=".28" stroke-width="4" />
        </pattern>
        {{-- Seta apontando para a direita, centrada em (0,0); gire com transform --}}
        <symbol id="mapa-seta" viewBox="-15 -12 30 24" overflow="visible">
            <path d="M-15 -4h15v-8l15 12-15 12v-8h-15z" fill="#e00000" />
        </symbol>
        <symbol id="mapa-seta-dupla" viewBox="-15 -7 30 14" overflow="visible">
            <path d="M-15 0l7-7v4h16v-4l7 7-7 7v-4h-16v4z" fill="#e00000" />
        </symbol>
        {{-- Pino de localização: a ponta fica em (0, 30) --}}
        <symbol id="mapa-pino" viewBox="-9 -9 18 40" overflow="visible">
            <line x1="0" y1="2" x2="0" y2="30" stroke="#d9d9d9" stroke-width="1.6" />
            <circle r="7.5" fill="#e53935" stroke="#a31515" stroke-width="1" />
            <circle cx="-2.4" cy="-2.6" r="2.2" fill="#fff" fill-opacity=".55" />
        </symbol>
        <symbol id="mapa-homem" viewBox="0 0 20 44">
            <circle cx="10" cy="5" r="4.6" fill="#f5f5f5" />
            <path d="M4.5 11h11a2.5 2.5 0 0 1 2.5 2.5V27h-3v16h-4V30h-2v13H5V27H2V13.5A2.5 2.5 0 0 1 4.5 11z" fill="#f5f5f5" />
        </symbol>
        <symbol id="mapa-mulher" viewBox="0 0 20 44">
            <circle cx="10" cy="5" r="4.6" fill="#f5f5f5" />
            <path d="M6.5 11h7l5.5 18h-4.5v14h-3V31h-3v12h-3V29H1z" fill="#f5f5f5" />
        </symbol>
    </defs>
</svg>
