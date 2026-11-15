{{--
    Estradas pela linha central: $vias = [[d, largura], ...].
    Desenha todos os contornos primeiro e todo o asfalto depois, para que cruzamentos e
    entroncamentos se fundam (sem o contorno de uma via cortando a outra).
--}}
@foreach ($vias as [$d, $l])
    <path d="{{ $d }}" class="estrada-borda" stroke-width="{{ $l + 3 }}" />
@endforeach
@foreach ($vias as [$d, $l])
    <path d="{{ $d }}" class="estrada" stroke-width="{{ $l }}" />
@endforeach
