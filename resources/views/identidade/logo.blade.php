{{--
    Logo da edição. Usa a imagem de config('identidade.logo') se existir;
    senão desenha a nuvem em SVG na cor de destaque (com brilho neon).
    Uso: @include('identidade.logo', ['classe' => 'size-10'])
--}}
@php $classe = $classe ?? 'size-9'; @endphp
@if (config('identidade.logo'))
    <img src="{{ asset(config('identidade.logo')) }}" alt="{{ config('identidade.sigla') }}" class="{{ $classe }} shrink-0 object-contain">
@else
    <svg viewBox="0 0 64 48" fill="none" class="{{ $classe }} logo-neon shrink-0 text-brand-600" role="img" aria-label="{{ config('identidade.sigla') }}">
        {{-- Nuvem externa --}}
        <path d="M15 42h33a10.5 10.5 0 0 0 1.5-20.9A15 15 0 0 0 21 15.5 13.3 13.3 0 0 0 15 42Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round" />
        {{-- Segunda nuvem sobreposta (como no logo das artes) --}}
        <path d="M29 42a7.5 7.5 0 0 1 1.5-14.8 9.5 9.5 0 0 1 18.5-6.1" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@endif
