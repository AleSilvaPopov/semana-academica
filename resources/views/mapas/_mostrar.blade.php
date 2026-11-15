{{-- Mostra um mapa do config: desenho SVG (view) ou imagem (url). $m = item do mapa; $classe opcional para o <img>. --}}
@if ($m['view'])
    @include($m['view'])
@else
    <img src="{{ $m['url'] }}" alt="Mapa: {{ $m['titulo'] }} — {{ $m['detalhe'] }}" loading="lazy" class="{{ $classe ?? 'h-full w-full object-cover' }}">
@endif
