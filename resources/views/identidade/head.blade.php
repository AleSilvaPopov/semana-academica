{{--
    Injeta a identidade visual (config/identidade.php) no <head> de todos os layouts:
    fontes do Google Fonts e as variáveis CSS de onde o app.css calcula a paleta.
--}}
@php
    $id = config('identidade');
    // "Inter:wght@400;600" -> família "Inter" + parâmetro completo para a URL.
    $familias = collect($id['fontes'] ?? [])
        ->filter()
        ->unique()
        ->mapWithKeys(fn ($f) => [$f => trim(\Illuminate\Support\Str::before($f, ':'))]);
    $nomeFonte = fn ($chave, $padrao) => isset($id['fontes'][$chave])
        ? "'".$familias[$id['fontes'][$chave]]."', ".$padrao
        : $padrao;
    $hex = fn ($c, $padrao) => preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $c) ? $c : $padrao;
@endphp
<meta name="theme-color" content="{{ $hex($id['cores']['fundo'] ?? null, '#050505') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
{{-- Um <link> por família: se um nome estiver errado, só aquela fonte deixa de carregar. --}}
@foreach ($familias as $param => $nome)
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $param) }}&display=swap">
@endforeach
<style>
    :root {
        --tema-destaque: {{ $hex($id['cores']['destaque'] ?? null, '#e60000') }};
        --tema-fundo: {{ $hex($id['cores']['fundo'] ?? null, '#050505') }};
        --tema-texto: {{ $hex($id['cores']['texto'] ?? null, '#f5f5f5') }};
        {{-- Opcionais: sem valor, o CSS deriva do destaque/fundo --}}
        @if (! empty($id['cores']['neon']))--tema-neon: {{ $hex($id['cores']['neon'], '#ff1a1a') }};@endif
        @if (! empty($id['cores']['superficie']))--tema-superficie: {{ $hex($id['cores']['superficie'], '#0a0a0a') }};@endif
        --tema-brilho: {{ (float) ($id['brilho'] ?? 0.6) }};
        --tema-fonte-titulo: {!! $nomeFonte('titulo', 'ui-sans-serif, system-ui, sans-serif') !!};
        --tema-fonte-display: {!! $nomeFonte('display', 'var(--tema-fonte-titulo)') !!};
        --tema-fonte-corpo: {!! $nomeFonte('corpo', 'ui-sans-serif, system-ui, sans-serif') !!};
        --tema-fonte-mono: {!! $nomeFonte('mono', 'ui-monospace, monospace') !!};
    }
</style>
