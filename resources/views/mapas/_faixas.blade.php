{{--
    Moldura comum das plantas: faixa ondulada em cima e embaixo, fundo do corredor e a instituição.
    Parâmetros: $topo e $base (y onde a planta começa/termina), $sigla, $nome.
--}}
<rect width="600" height="{{ $topo }}" fill="url(#mapa-faixa-topo)" />
<rect width="600" height="{{ $topo }}" fill="url(#mapa-ondas)" />
<rect y="{{ $base }}" width="600" height="{{ 570 - $base }}" fill="url(#mapa-faixa-base)" />
<rect y="{{ $base }}" width="600" height="{{ 570 - $base }}" fill="url(#mapa-ondas)" />
<rect y="{{ $topo }}" width="600" height="{{ $base - $topo }}" fill="#050505" />
<text x="582" y="50" class="sigla">{{ $sigla }}</text>
<text x="582" y="68" class="inst">{{ $nome }}</text>
