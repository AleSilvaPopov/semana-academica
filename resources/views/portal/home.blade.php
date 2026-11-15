<x-layouts.portal>
    @if (! $edicao)
        <section class="mx-auto max-w-3xl px-4 py-24 text-center">
            @include('identidade.logo', ['classe' => 'mx-auto mb-6 h-16 w-24'])
            <h1 class="neon-claro text-3xl font-semibold text-zinc-950">{{ \App\Support\Identidade::nomeCompleto() }}</h1>
            <p class="mt-3 text-zinc-500">Nenhuma edição cadastrada ainda.</p>
        </section>
    @else
        @php
            // "Semana Integrada de Computação" -> "Semana Integrada de" + "Computação" (como nas artes).
            $partes = preg_split('/(?<=\sde)\s/iu', $edicao->nome_evento, 2);
            $linhaMenor = count($partes) === 2 ? $partes[0] : null;
            $linhaMaior = count($partes) === 2 ? $partes[1] : $edicao->nome_evento;
            $chamada = config('identidade.chamada');
            $mostrarChamada = $chamada && $edicao->data_termino->endOfDay()->isFuture();
        @endphp

        {{-- Destaque da edição mais recente --}}
        <section class="relative overflow-hidden">
            <div class="relative mx-auto flex max-w-4xl flex-col items-center px-4 pt-16 pb-24 text-center sm:px-6 sm:pt-20 sm:pb-28">
                @include('identidade.logo', ['classe' => 'h-16 w-24 sm:h-20 sm:w-28'])

                <p class="rotulo-mono mt-6 text-brand-700">{{ $edicao->edicao }}ª edição</p>

                <h1 class="mt-4 text-zinc-950">
                    @if ($linhaMenor)
                        <span class="block font-display text-xl tracking-wide sm:text-3xl">{{ $linhaMenor }}</span>
                    @endif
                    <span class="neon-claro mt-1 block font-display text-3xl leading-tight font-bold break-words sm:text-6xl">{{ $linhaMaior }}</span>
                    <span class="mt-3 block font-titulo text-2xl font-semibold tracking-[0.5em] text-zinc-800 sm:text-4xl" style="padding-left: 0.5em">{{ $edicao->data_inicio->year }}</span>
                </h1>

                @if ($mostrarChamada)
                    <p class="mt-8 font-mono text-lg font-bold tracking-[0.15em] text-zinc-950 uppercase sm:text-2xl">
                        {{ $chamada }}<span class="ml-1 inline-block w-[0.6em] animate-pulse text-brand-600">_</span>
                    </p>
                @endif

                <div class="mt-8 flex flex-col items-center gap-2 text-sm text-zinc-600 sm:flex-row sm:gap-6">
                    <span class="flex items-center gap-2"><x-icon name="calendar" class="size-5 text-brand-600" /> {{ $edicao->data_inicio->translatedFormat('d') }} a {{ $edicao->data_termino->translatedFormat('d \d\e F \d\e Y') }}</span>
                    <span class="flex items-center gap-2"><x-icon name="map" class="size-5 text-brand-600" /> {{ $edicao->local_sede }}</span>
                </div>
                @if ($edicao->observacoes)
                    <p class="mt-4 max-w-2xl text-sm text-zinc-500">{{ $edicao->observacoes }}</p>
                @endif

                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <x-button tamanho="lg" :href="route('portal.programacao')">Ver programação</x-button>
                    @guest('participante')
                        <x-button tamanho="lg" variante="secundario" :href="route('portal.cadastro')">Criar minha conta</x-button>
                    @else
                        <x-button tamanho="lg" variante="secundario" :href="route('portal.inscricoes')">Minhas inscrições</x-button>
                    @endguest
                </div>

                @if (config('identidade.instituicoes'))
                    <div class="mt-14 flex flex-wrap items-center justify-center gap-x-8 gap-y-2 font-mono text-[11px] tracking-[0.18em] text-zinc-500 uppercase">
                        @foreach (config('identidade.instituicoes') as $inst)
                            <span>{{ $inst }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="divisor-neon"></div>
        </section>

        {{-- Números --}}
        <section class="mx-auto -mt-px max-w-6xl px-4 sm:px-6">
            <div class="grid grid-cols-2 gap-px overflow-hidden rounded-b-xl border-x border-b border-zinc-200 bg-zinc-200 sm:grid-cols-4">
                @foreach (['Atividades' => $totais['atividades'], 'Ministrantes' => $totais['ministrantes'], 'Vagas' => $totais['vagas'], 'Dias de evento' => $totais['dias']] as $rotulo => $valor)
                    <div class="bg-superficie px-5 py-6 text-center">
                        <p class="neon font-mono text-3xl font-bold text-brand-700 tabular-nums sm:text-4xl">{{ $valor }}</p>
                        <p class="rotulo-mono mt-1 text-[10px] text-zinc-500">{{ $rotulo }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Destaques da programação --}}
        <section class="mx-auto max-w-6xl px-4 pt-16 sm:px-6">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="rotulo-mono text-[11px] text-brand-700">Programação</p>
                    <h2 class="mt-1 text-2xl font-semibold text-zinc-950">Destaques</h2>
                    <p class="text-sm text-zinc-500">Palestras, minicursos e workshops desta edição.</p>
                </div>
                <a href="{{ route('portal.programacao') }}" class="link shrink-0 text-sm">Programação completa →</a>
            </div>
            @if ($destaques->isEmpty())
                <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500">A programação ainda está sendo montada.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($destaques as $atividade)
                        <div>
                            <p class="rotulo-mono mb-2 text-[10px] text-zinc-500">{{ $atividade->data_realizacao->translatedFormat('l, d/m') }}</p>
                            @include('portal._cartao-atividade', ['atividade' => $atividade])
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Patrocinadores --}}
        <section class="mx-auto max-w-6xl px-4 pt-16 sm:px-6">
            <p class="rotulo-mono text-[11px] text-brand-700">Apoio</p>
            <h2 class="mt-1 text-2xl font-semibold text-zinc-950">Patrocinadores</h2>
            <p class="text-sm text-zinc-500">Quem torna esta edição possível.</p>
            @if ($edicao->patrocinios->isEmpty())
                <p class="mt-4 text-sm text-zinc-500">Nenhum patrocinador confirmado ainda.</p>
            @else
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($edicao->patrocinios as $p)
                        <div class="cartao flex flex-col justify-between p-4 transition hover:border-brand-400">
                            <p class="font-semibold text-zinc-900">{{ $p->nome_patrocinador }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                <x-badge :enum="$p->tipo_patrocinio" />
                                <span class="text-xs text-zinc-500">{{ $p->descricao_patrocinio }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</x-layouts.portal>
