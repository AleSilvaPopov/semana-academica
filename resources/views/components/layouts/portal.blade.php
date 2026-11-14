@props(['titulo' => null])
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $titulo ? $titulo.' · ' : '' }}Semana Acadêmica de Computação · UFGD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-white">
<header x-data="{ menu: false }" class="sticky top-0 z-30 border-b border-zinc-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
        <a href="{{ route('portal.home') }}" class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">SA</span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-zinc-900">Semana Acadêmica</span>
                <span class="block text-xs text-zinc-500">Computação · UFGD</span>
            </span>
        </a>

        @php
            $links = [
                ['portal.home', 'Início', 'portal.home'],
                ['portal.programacao', 'Programação', 'portal.programacao*'],
            ];
            if (auth('participante')->check()) {
                $links[] = ['portal.inscricoes', 'Minhas inscrições', 'portal.inscricoes*'];
                $links[] = ['portal.certificados', 'Certificados', 'portal.certificados*'];
                $links[] = ['portal.perfil', 'Meu perfil', 'portal.perfil*'];
            }
        @endphp

        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($links as [$rota, $rotulo, $padrao])
                @if (Route::has($rota))
                    <a href="{{ route($rota) }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($padrao) ? 'text-brand-700' : 'text-zinc-600 hover:text-zinc-900' }}">{{ $rotulo }}</a>
                @endif
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 md:flex">
            @auth('participante')
                <span class="max-w-40 truncate text-sm text-zinc-600">{{ \Illuminate\Support\Str::of(auth('participante')->user()->nome_completo)->before(' ') }}</span>
                <form method="POST" action="{{ route('portal.logout') }}">
                    @csrf
                    <x-button type="submit" variante="secundario" tamanho="sm" icone="logout">Sair</x-button>
                </form>
            @else
                @if (Route::has('portal.login'))
                    <x-button variante="fantasma" tamanho="sm" :href="route('portal.login')">Entrar</x-button>
                    <x-button tamanho="sm" :href="route('portal.cadastro')">Criar conta</x-button>
                @endif
            @endauth
        </div>

        <button type="button" @click="menu = !menu" class="rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 md:hidden" aria-label="Menu">
            <x-icon x-show="!menu" name="menu" class="size-6" />
            <x-icon x-show="menu" x-cloak name="x" class="size-6" />
        </button>
    </div>

    {{-- Menu no celular --}}
    <div x-show="menu" x-cloak x-transition class="border-t border-zinc-100 px-4 pb-4 md:hidden">
        <nav class="flex flex-col py-2">
            @foreach ($links as [$rota, $rotulo, $padrao])
                @if (Route::has($rota))
                    <a href="{{ route($rota) }}" class="rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($padrao) ? 'bg-brand-50 text-brand-700' : 'text-zinc-700' }}">{{ $rotulo }}</a>
                @endif
            @endforeach
        </nav>
        @auth('participante')
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <x-button type="submit" variante="secundario" class="w-full" icone="logout">Sair</x-button>
            </form>
        @else
            @if (Route::has('portal.login'))
                <div class="grid grid-cols-2 gap-2">
                    <x-button variante="secundario" :href="route('portal.login')">Entrar</x-button>
                    <x-button :href="route('portal.cadastro')">Criar conta</x-button>
                </div>
            @endif
        @endauth
    </div>
</header>

<main class="flex-1">
    @if (session('sucesso') || session('erro') || session('aviso') || $errors->any())
        <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6"><x-flash /></div>
    @endif
    {{ $slot }}
</main>

<footer class="mt-16 border-t border-zinc-200 bg-zinc-50">
    <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-8 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p>Semana Acadêmica de Computação · Universidade Federal da Grande Dourados</p>
        <a href="{{ route('admin.login') }}" class="hover:text-zinc-800">Área da organização →</a>
    </div>
</footer>
</body>
</html>
