@props(['titulo' => 'Painel'])
@php use App\Support\Identidade; @endphp
<!DOCTYPE html>
<html lang="pt-BR" class="h-full" {!! Identidade::atributosHtml() !!}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Identidade::tituloPagina($titulo.' · Painel') }}</title>
    @include('identidade.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div x-data="{ menu: false }" class="min-h-full">
    {{-- Fundo escuro do menu no celular --}}
    <div x-show="menu" x-cloak x-transition.opacity @click="menu = false" class="fixed inset-0 z-30 bg-black/60 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="menu ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-zinc-200 bg-superficie/95 backdrop-blur-md transition-transform lg:translate-x-0">
        <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-zinc-200 px-5">
            @include('identidade.logo', ['classe' => 'size-8'])
            <div class="leading-tight">
                @include('identidade.marca', ['icone' => false, 'tamanho' => 'text-base'])
                <p class="mt-1 font-mono text-[10px] tracking-[0.18em] text-zinc-500 uppercase">Painel da organização</p>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4">
            @foreach (\App\Support\MenuAdmin::grupos() as $grupo)
                @php
                    $itensVisiveis = collect($grupo['itens'])->filter(
                        fn ($i) => empty($i['pode']) || (auth('integrante')->check() && auth('integrante')->user()->can($i['pode']))
                    );
                @endphp
                @if ($itensVisiveis->isNotEmpty())
                    <p class="mt-4 mb-1 px-2 font-mono text-[10px] font-bold tracking-[0.2em] text-zinc-400 uppercase first:mt-0">{{ $grupo['grupo'] }}</p>
                    <ul class="space-y-0.5">
                        @foreach ($itensVisiveis as $item)
                            @php $ativo = request()->routeIs($item['ativo']); @endphp
                            <li>
                                <a href="{{ route($item['rota']) }}"
                                    class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm font-medium transition {{ $ativo ? 'bg-brand-50 text-brand-800 shadow-[inset_2px_0_0_var(--tema-destaque)]' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                                    <x-icon :name="$item['icone']" class="size-5 {{ $ativo ? 'text-brand-600' : 'text-zinc-400' }}" />
                                    {{ $item['rotulo'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-zinc-200 p-3">
            <a href="{{ route('portal.home') }}" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800">
                <x-icon name="home" class="size-5 text-zinc-400" /> Ver portal público
            </a>
        </div>
    </aside>

    <div class="lg:pl-64">
        {{-- Cabeçalho: seletor de edição + usuário --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-zinc-200 bg-fundo/80 px-4 backdrop-blur-md sm:px-6">
            <button type="button" @click="menu = true" class="-ml-1 rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 lg:hidden" aria-label="Abrir menu">
                <x-icon name="menu" class="size-6" />
            </button>

            @isset($edicoesMenu)
                <form method="POST" action="{{ route('admin.edicao.selecionar') }}" class="flex min-w-0 items-center gap-2">
                    @csrf
                    <label for="seletor-edicao" class="hidden text-xs font-medium text-zinc-500 sm:block">Edição</label>
                    <select id="seletor-edicao" name="id_comissao" onchange="this.form.submit()"
                        class="campo w-auto max-w-[14rem] py-1.5 pr-8 font-medium sm:max-w-xs">
                        @foreach ($edicoesMenu as $ed)
                            <option value="{{ $ed->id_comissao }}" @selected($edicaoAtual?->id_comissao == $ed->id_comissao)>
                                {{ $ed->edicao }}ª edição ({{ \Illuminate\Support\Carbon::parse($ed->data_inicio)->year }})
                            </option>
                        @endforeach
                    </select>
                    <noscript><x-button type="submit" tamanho="sm" variante="secundario">Trocar</x-button></noscript>
                </form>
            @endisset

            <div class="ml-auto flex items-center gap-3">
                @auth('integrante')
                    @php $eu = auth('integrante')->user(); @endphp
                    <div class="hidden text-right leading-tight sm:block">
                        <p class="text-sm font-medium text-zinc-900">{{ $eu->nome_completo }}</p>
                        <p class="text-xs text-zinc-500">{{ $papelNaEdicao ?? $eu->funcao }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <x-button type="submit" variante="fantasma" tamanho="sm" icone="logout" title="Sair"><span class="hidden sm:inline">Sair</span></x-button>
                    </form>
                @endauth
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-8">
            <x-flash class="mb-6" />
            <div class="mt-0">{{ $slot }}</div>
        </main>
    </div>
</div>
</body>
</html>
