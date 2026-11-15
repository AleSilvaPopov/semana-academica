{{--
    Rótulo nítido sobre mapa em imagem: caixa escura com borda neon centrada em ($x, $y).
    $texto aceita várias linhas separadas por "\n".
--}}
@php
    $linhas = explode("\n", $texto);
    $largura = max(array_map('mb_strlen', $linhas)) * 7.9 + 18;
    $altura = count($linhas) * 16 + 6;
@endphp
<rect x="{{ $x - $largura / 2 }}" y="{{ $y - $altura / 2 }}" width="{{ $largura }}" height="{{ $altura }}" rx="4" class="rotulo-bg" />
@foreach ($linhas as $i => $linha)
    <text x="{{ $x }}" y="{{ $y - (count($linhas) - 1) * 8 + $i * 16 + 0.5 }}" class="rotulo">{{ $linha }}</text>
@endforeach
