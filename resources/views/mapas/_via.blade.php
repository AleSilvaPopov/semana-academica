{{--
    Via desenhada pela linha central ($d).
    - estrada: $largura (px) → contorno colorido + asfalto preto por cima
    - rua: sem $largura → linha dupla fina (contorno + miolo da cor do fundo); $clara = miolo na cor das áreas claras
--}}
@isset($largura)
    <path d="{{ $d }}" class="estrada-borda" stroke-width="{{ $largura + 3 }}" />
    <path d="{{ $d }}" class="estrada" stroke-width="{{ $largura }}" />
@else
    <path d="{{ $d }}" class="rua-borda" />
    <path d="{{ $d }}" class="rua {{ ($clara ?? false) ? 'clara' : '' }}" />
@endisset
