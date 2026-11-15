@props(['titulo' => null])
{{-- Layout centralizado para login, cadastro e páginas de erro. --}}
@php use App\Support\Identidade; @endphp
<!DOCTYPE html>
<html lang="pt-BR" class="h-full" {!! Identidade::atributosHtml() !!}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Identidade::tituloPagina($titulo) }}</title>
    @include('identidade.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center px-4 py-10">
    <div {{ $attributes->merge(['class' => 'w-full max-w-md']) }}>
        <a href="{{ url('/') }}" class="mb-8 flex flex-col items-center gap-3 text-center">
            @include('identidade.marca', ['tamanho' => 'text-2xl'])
            <span class="font-mono text-[11px] tracking-[0.25em] text-zinc-500 uppercase">{{ config('identidade.nome') }}</span>
        </a>
        {{ $slot }}
    </div>
</body>
</html>
