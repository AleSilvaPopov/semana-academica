@props(['titulo' => null])
@php
    use App\Support\Identidade;

    $home = route('portal.home');
    // Âncoras das seções da página inicial (a Maratona some se não estiver no config).
    $secoes = array_filter([
        ['Programação', $home.'#programacao'],
        config('identidade.maratona') ? ['Maratona', $home.'#maratona'] : null,
        ['Palestras', $home.'#palestras'],
        ['Minicursos', $home.'#minicursos'],
        ['Mapa', $home.'#mapa'],
    ]);
    $conta = [
        ['portal.inscricoes', 'Minhas inscrições', 'clipboard'],
        ['portal.certificados', 'Certificados', 'academic'],
        ['portal.perfil', 'Meu perfil', 'user'],
    ];
    $participante = auth('participante')->user();
@endphp
<!DOCTYPE html>
<html lang="pt-BR" class="h-full scroll-smooth" {!! Identidade::atributosHtml() !!}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Identidade::tituloPagina($titulo) }}</title>
    @include('identidade.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col">
<header x-data="{ menu: false }" class="sticky top-0 z-40 border-b border-neon/15 bg-fundo/85 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
        <a href="{{ $home }}" aria-label="Página inicial">@include('identidade.marca')</a>

        <nav class="hidden items-center gap-7 lg:flex">
            @foreach ($secoes as [$rotulo, $url])
                <a href="{{ $url }}" class="text-sm text-zinc-700 transition hover:text-zinc-950">{{ $rotulo }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            @if ($participante)
                <div x-data="{ aberto: false }" class="relative" @click.outside="aberto = false">
                    <button type="button" @click="aberto = !aberto" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-800 hover:bg-zinc-100">
                        <x-icon name="user" class="size-4 text-neon" />
                        {{ \Illuminate\Support\Str::of($participante->nome_completo)->before(' ') }}
                        <x-icon name="chevron-down" class="size-4 text-zinc-500" />
                    </button>
                    <div x-show="aberto" x-cloak x-transition class="glass-card absolute right-0 mt-2 w-56 bg-superficie p-1.5 shadow-xl">
                        @foreach ($conta as [$rota, $rotulo, $icone])
                            <a href="{{ route($rota) }}" class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm {{ request()->routeIs($rota.'*') ? 'bg-brand-50 text-brand-700' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <x-icon :name="$icone" class="size-4" /> {{ $rotulo }}
                            </a>
                        @endforeach
                        <form method="POST" action="{{ route('portal.logout') }}" class="mt-1 border-t border-zinc-200 pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950">
                                <x-icon name="logout" class="size-4" /> Sair
                            </button>
                        </form>
                    </div>
                </div>
                <a href="{{ route('portal.programacao') }}" class="btn-neon px-4 py-2 text-xs">Inscrever-se</a>
            @else
                <a href="{{ route('portal.login') }}" class="px-2 text-sm text-zinc-700 hover:text-zinc-950">Entrar</a>
                <a href="{{ route('portal.cadastro') }}" class="btn-neon px-4 py-2 text-xs">Inscreva-se</a>
            @endif
        </div>

        <button type="button" @click="menu = !menu" class="rounded-lg border border-neon/30 p-2 text-zinc-800 lg:hidden" aria-label="Menu">
            <x-icon x-show="!menu" name="menu" class="size-5" />
            <x-icon x-show="menu" x-cloak name="x" class="size-5" />
        </button>
    </div>

    {{-- Menu no celular --}}
    <div x-show="menu" x-cloak x-transition class="border-t border-neon/15 px-4 pb-5 lg:hidden">
        <nav class="flex flex-col py-2">
            @foreach ($secoes as [$rotulo, $url])
                <a href="{{ $url }}" @click="menu = false" class="rounded-lg px-3 py-2.5 text-sm text-zinc-800">{{ $rotulo }}</a>
            @endforeach
            @if ($participante)
                <p class="mt-3 mb-1 px-3 rotulo-secao text-[10px]">Minha conta</p>
                @foreach ($conta as [$rota, $rotulo, $icone])
                    <a href="{{ route($rota) }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs($rota.'*') ? 'bg-brand-50 text-brand-700' : 'text-zinc-800' }}">
                        <x-icon :name="$icone" class="size-4" /> {{ $rotulo }}
                    </a>
                @endforeach
            @endif
        </nav>
        @if ($participante)
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <button type="submit" class="btn-outline w-full">Sair</button>
            </form>
        @else
            <div class="grid gap-2">
                <a href="{{ route('portal.cadastro') }}" class="btn-neon">Inscreva-se</a>
                <a href="{{ route('portal.login') }}" class="btn-outline">Entrar</a>
            </div>
        @endif
    </div>
</header>

<main class="flex-1">
    @if (session('sucesso') || session('erro') || session('aviso') || $errors->any())
        <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6"><x-flash /></div>
    @endif
    {{ $slot }}
</main>

<footer class="mt-20 border-t border-neon/15">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <a href="{{ $home }}">@include('identidade.marca', ['icone' => false, 'tamanho' => 'text-base'])</a>
        <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-zinc-700">
            <a href="{{ route('portal.programacao') }}" class="hover:text-zinc-950">Programação</a>
            @if (config('identidade.maratona'))<a href="{{ $home }}#maratona" class="hover:text-zinc-950">Maratona</a>@endif
            <a href="{{ $home }}#mapa" class="hover:text-zinc-950">Mapa</a>
            <a href="{{ route('admin.login') }}" class="hover:text-zinc-950">Organização</a>
        </nav>
        <p class="text-xs text-zinc-500">{{ config('identidade.subtitulo') }} — {{ config('identidade.ano') }}</p>
    </div>
</footer>
</body>
</html>
