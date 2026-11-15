{{--
    Ícone da edição. Usa a imagem de config('identidade.logo') se existir;
    senão desenha o ícone de circuito em SVG na cor neon.
    Uso: @include('identidade.logo', ['classe' => 'size-8'])
--}}
@php $classe = $classe ?? 'size-8'; @endphp
@if (config('identidade.logo'))
    <img src="{{ asset(config('identidade.logo')) }}" alt="{{ config('identidade.sigla') }}" class="{{ $classe }} shrink-0 object-contain">
@else
    <svg viewBox="0 0 32 32" fill="none" class="{{ $classe }} logo-neon shrink-0 text-neon" role="img" aria-label="{{ config('identidade.sigla') }}">
        <rect x="1.5" y="1.5" width="29" height="29" rx="7" stroke="currentColor" stroke-width="2" />
        <path d="M8 11h7l3 3h6M8 16h4M16 16h2l3 3h3M8 21h9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="24" cy="14" r="1.6" fill="currentColor" />
        <circle cx="24" cy="19" r="1.6" fill="currentColor" />
    </svg>
@endif
