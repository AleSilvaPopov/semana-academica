{{--
    Página inicial (landing) — design "SIC//2026".
    Conteúdo do banco: edição, palestras, minicursos, programação, locais, patrocinadores.
    Conteúdo do config/identidade.php: textos de marca, maratona, pontos fixos do mapa.
--}}
<x-layouts.portal>
    @if (! $edicao)
        <section class="mx-auto max-w-3xl px-4 py-28 text-center">
            @include('identidade.marca', ['tamanho' => 'text-3xl'])
            <p class="mt-4 text-zinc-500">Nenhuma edição cadastrada ainda.</p>
        </section>
    @else
        @php
            $chamada = config('identidade.chamada');
            $aberta = $edicao->data_termino->endOfDay()->isFuture();
            $maratona = config('identidade.maratona');
            $urlInscricao = auth('participante')->check() ? route('portal.programacao') : route('portal.cadastro');
            $periodo = $edicao->data_inicio->translatedFormat('d').' a '.$edicao->data_termino->translatedFormat('d \d\e F \d\e Y');
            $extensoes = ['C++' => 'main.cpp', 'Python' => 'main.py', 'Java' => 'Main.java'];
        @endphp

        {{-- ============ TOPO ============ --}}
        <section class="relative overflow-hidden">
            {{-- Trilhas de circuito decorativas (esquerda e direita) --}}
            @foreach (['left-0', 'right-0 -scale-x-100'] as $lado)
                <svg class="pointer-events-none absolute top-10 hidden h-[380px] w-[300px] text-neon md:block {{ $lado }}" viewBox="0 0 300 380" fill="none" aria-hidden="true">
                    <g stroke="currentColor" stroke-opacity=".45" stroke-width="1.2">
                        <path d="M0 50h110l25 25h70" /><path d="M135 0v45" /><path d="M0 120h50l22-22h130" />
                        <path d="M30 230h140l25-25h45" /><path d="M0 290h160l25 25h55" /><path d="M200 160h60" />
                    </g>
                    <g fill="currentColor">
                        <circle cx="205" cy="75" r="3" /><circle cx="135" cy="45" r="3" /><circle cx="202" cy="98" r="3" />
                        <circle cx="240" cy="205" r="3" /><circle cx="240" cy="315" r="3" />
                    </g>
                    <circle cx="263" cy="160" r="5" stroke="currentColor" stroke-width="1.5" />
                </svg>
            @endforeach

            <div class="relative mx-auto flex max-w-4xl flex-col items-center px-4 pt-20 pb-16 text-center sm:px-6 sm:pt-24">
                @if ($chamada && $aberta)
                    <span class="badge-live">{{ $chamada }}</span>
                @endif

                <h1 class="mt-7 font-display uppercase">
                    <span class="block text-4xl leading-[1.02] font-bold text-zinc-950 sm:text-6xl">{{ config('identidade.nome') }}</span>
                    <span class="text-glow mt-2 block text-[5.5rem] leading-none font-bold sm:text-[9rem]">{{ $edicao->data_inicio->year }}</span>
                </h1>

                @if (config('identidade.descricao'))
                    <p class="mt-7 max-w-xl text-base leading-relaxed text-zinc-600 sm:text-lg">{{ config('identidade.descricao') }}</p>
                @endif
                <p class="mt-5 font-mono text-xs tracking-[0.14em] text-zinc-800 uppercase">{{ $periodo }} · {{ $edicao->local_sede }}</p>

                <div class="mt-9 flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <a href="{{ $urlInscricao }}" class="btn-neon">Inscreva-se <x-icon name="arrow-right" class="size-4" /></a>
                    <a href="#programacao" class="btn-outline">Ver programação</a>
                </div>
            </div>

            @if (config('identidade.realizacao'))
                <div class="mx-auto max-w-5xl px-4 sm:px-6">
                    <div class="divisor-neon"></div>
                    <div class="flex items-center justify-center gap-6 py-7 sm:gap-10">
                        <span class="font-mono text-[10px] tracking-[0.25em] text-zinc-500 uppercase">Realização</span>
                        @foreach (config('identidade.realizacao') as $i => $inst)
                            @if ($i > 0)<span class="size-1.5 rounded-full bg-neon shadow-[0_0_8px_var(--tema-neon)]"></span>@endif
                            <span class="font-display text-lg font-bold tracking-wider text-zinc-800">{{ $inst }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        {{-- ============ 01 // MARATONA (config) ============ --}}
        @php $n = 0; @endphp
        @if ($maratona)
            @php $n++; $linguagens = $maratona['linguagens'] ?? []; @endphp
            <section id="maratona" class="scroll-mt-20">
                <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                    <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Competição</p>
                    <h2 class="titulo-secao mt-3">Maratona de<br><span class="text-neon">programação</span></h2>
                    <p class="mt-4 max-w-2xl text-zinc-600">{{ $maratona['descricao'] ?? '' }}</p>

                    <div class="mt-10 grid gap-4 lg:grid-cols-[1.15fr_1fr]">
                        {{-- Janela de editor com abas por linguagem --}}
                        <div x-data="{ ling: @js($linguagens[0] ?? '') }" class="glass-card flex flex-col overflow-hidden">
                            <div class="flex items-center justify-between gap-3 border-b border-neon/15 px-4 py-3">
                                <div class="flex gap-1.5" aria-hidden="true">
                                    <span class="size-2.5 rounded-full bg-neon"></span><span class="size-2.5 rounded-full bg-neon/40"></span><span class="size-2.5 rounded-full bg-zinc-300"></span>
                                </div>
                                <div class="flex gap-1.5">
                                    @foreach ($linguagens as $l)
                                        <button type="button" @click="ling = @js($l)" :class="ling === @js($l) && 'chip-ativo'" class="chip">{{ $l }}</button>
                                    @endforeach
                                </div>
                            </div>
                            @foreach ($linguagens as $l)
                                <pre x-show="ling === @js($l)" @if (! $loop->first) x-cloak @endif class="flex-1 overflow-x-auto p-5 font-mono text-[13px] leading-relaxed text-zinc-900"><code>{{ $maratona['codigo'][$l] ?? '' }}</code></pre>
                            @endforeach
                            <div class="border-t border-neon/15 px-5 py-3 font-mono text-xs text-zinc-500">
                                $ @foreach ($linguagens as $l)<span x-show="ling === @js($l)" @if (! $loop->first) x-cloak @endif class="text-neon-soft">{{ $extensoes[$l] ?? 'main' }}</span>@endforeach — Accepted ✓
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ([
                                ['code', 'Linguagens', null],
                                ['users', 'Equipes', $maratona['equipes'] ?? null],
                                ['clock', 'Duração', $maratona['duracao'] ?? null],
                                ['trophy', 'Premiação', $maratona['premiacao'] ?? null],
                            ] as [$icone, $titulo, $texto])
                                <div class="glass-card p-5">
                                    <x-icon :name="$icone" class="size-6 text-neon" />
                                    <h3 class="mt-4 font-display text-lg font-semibold text-zinc-950">{{ $titulo }}</h3>
                                    @if ($titulo === 'Linguagens')
                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            @foreach ($linguagens as $l)<span class="chip">{{ $l }}</span>@endforeach
                                        </div>
                                    @else
                                        <p class="mt-2 text-sm leading-relaxed text-zinc-600">{{ $texto }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ PALESTRAS (banco) ============ --}}
        @php $n++; @endphp
        <section id="palestras" class="scroll-mt-20 border-t border-neon/10">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Conhecimento</p>
                        <h2 class="titulo-secao mt-3">Palestras</h2>
                        <p class="mt-4 text-zinc-600">Pesquisa, mercado e carreira com convidados da academia e da indústria.</p>
                    </div>
                    <a href="{{ route('portal.programacao', ['tipo' => 'palestra']) }}" class="btn-outline shrink-0 self-start px-5 py-2.5 text-xs sm:self-auto">Ver todas</a>
                </div>

                @if ($palestras->isEmpty())
                    <p class="glass-card mt-10 p-8 text-center text-sm text-zinc-500">As palestras desta edição serão anunciadas em breve.</p>
                @else
                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        @foreach ($palestras as $p)
                            @php $m = $p->ministrantes->first()?->participante; @endphp
                            <a href="{{ route('portal.atividades.show', $p) }}" class="glass-card flex flex-col p-5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rotulo-secao text-[10px]">Palestra {{ sprintf('%02d', $loop->iteration) }}</span>
                                    <span class="font-mono text-[11px] text-zinc-500">{{ $p->data_realizacao->format('d/m') }} · {{ $p->data_realizacao->format('H:i') }}</span>
                                </div>
                                <h3 class="mt-3 font-display text-xl leading-snug font-semibold text-zinc-950">{{ $p->nome }}</h3>
                                <div class="mt-auto pt-5">
                                    <div class="flex items-center gap-3 border-t border-zinc-200 pt-4">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-neon text-neon shadow-[0_0_12px_-4px_var(--tema-neon)]">
                                            <x-icon name="user" class="size-5" />
                                        </span>
                                        <div class="min-w-0 text-sm">
                                            <p class="truncate font-semibold text-zinc-950">{{ $m?->nome_completo ?? 'A confirmar' }}@if ($p->ministrantes->count() > 1)<span class="font-normal text-zinc-500"> +{{ $p->ministrantes->count() - 1 }}</span>@endif</p>
                                            <p class="truncate text-zinc-500">{{ $m?->instituicao_origem }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- ============ MINICURSOS (banco) ============ --}}
        @php $n++; $icones = ['terminal', 'database', 'cpu']; @endphp
        <section id="minicursos" class="scroll-mt-20 border-t border-neon/10">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Mão na massa</p>
                <h2 class="titulo-secao mt-3">Minicursos</h2>
                <p class="mt-4 text-zinc-600">Aulas práticas em laboratório, com vagas limitadas e certificado.</p>

                @if ($minicursos->isEmpty())
                    <p class="glass-card mt-10 p-8 text-center text-sm text-zinc-500">Os minicursos desta edição serão anunciados em breve.</p>
                @else
                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        @foreach ($minicursos as $mc)
                            @php $restantes = $mc->vagas_restantes; @endphp
                            <div class="glass-card glass-card-hover flex flex-col p-5">
                                <span class="flex size-10 items-center justify-center rounded-lg border border-neon/40 bg-neon/10 text-neon">
                                    <x-icon :name="$icones[$loop->index % 3]" class="size-5" />
                                </span>
                                <h3 class="mt-4 font-display text-xl leading-snug font-semibold text-zinc-950">{{ $mc->nome }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-zinc-600">{{ $mc->descricao }}</p>
                                <div class="mt-4 flex flex-wrap gap-1.5">
                                    <span class="chip-neutro">{{ $mc->tipo->rotulo() }}</span>
                                    <span class="chip-neutro">{{ formata_duracao($mc->duracao_minutos) }}</span>
                                    <span class="chip-neutro">{{ $restantes === 0 ? 'Esgotado' : $restantes.' vaga'.($restantes > 1 ? 's' : '') }}</span>
                                </div>
                                <a href="{{ route('portal.atividades.show', $mc) }}" class="btn-outline mt-5 py-2.5 text-xs">{{ $restantes === 0 ? 'Ver detalhes' : 'Garantir vaga' }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- ============ PROGRAMAÇÃO (banco, abas por dia) ============ --}}
        @php $n++; @endphp
        <section id="programacao" class="scroll-mt-20 border-t border-neon/10">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Agenda</p>
                        <h2 class="titulo-secao mt-3">Programação</h2>
                    </div>
                    <a href="{{ route('portal.programacao') }}" class="link shrink-0 text-sm">Programação completa →</a>
                </div>

                @if ($dias->isEmpty())
                    <p class="glass-card mt-10 p-8 text-center text-sm text-zinc-500">A programação ainda está sendo montada.</p>
                @else
                    <div x-data="{ dia: 0 }" class="mt-8">
                        <div class="flex flex-wrap gap-2" role="tablist">
                            @foreach ($dias->keys() as $i => $data)
                                <button type="button" role="tab" @click="dia = {{ $i }}" :class="dia === {{ $i }} && 'chip-ativo'"
                                    class="chip px-3.5 py-2 text-xs uppercase">Dia {{ $i + 1 }} · {{ \Illuminate\Support\Carbon::parse($data)->format('d/m') }}</button>
                            @endforeach
                        </div>
                        @foreach ($dias->values() as $i => $atividadesDoDia)
                            <div x-show="dia === {{ $i }}" @if ($i > 0) x-cloak @endif class="glass-card mt-5 divide-y divide-zinc-200 overflow-hidden">
                                @foreach ($atividadesDoDia as $a)
                                    <a href="{{ route('portal.atividades.show', $a) }}" class="flex flex-wrap items-center gap-x-6 gap-y-2 px-5 py-4 transition hover:bg-zinc-100/60">
                                        <span class="w-14 font-mono text-sm text-neon-soft">{{ $a->data_realizacao->format('H:i') }}</span>
                                        <span class="min-w-0 flex-1 font-display font-semibold text-zinc-950">{{ $a->nome }}</span>
                                        <span class="tag">{{ $a->tipo->rotulo() }}</span>
                                        <span class="w-36 truncate text-right text-sm text-zinc-500 max-sm:w-auto max-sm:text-left">{{ $a->local?->nome_curto }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- ============ MAPA ============ --}}
        @php
            $n++;
            // Posições do mapa esquemático (1 à esquerda, 2 em cima, 3 à direita, 4 embaixo).
            $posicoes = ['left-[5%] top-1/2 -translate-y-1/2 h-16 w-20', 'left-1/2 top-[6%] -translate-x-1/2 h-16 w-24', 'right-[5%] top-1/2 -translate-y-1/2 h-20 w-28', 'left-1/2 bottom-[8%] -translate-x-1/2 h-14 w-24'];
        @endphp
        @if (! empty($mapas))
            {{-- Mapas reais (imagens do config): lista à esquerda + visualizador --}}
            <section id="mapa" class="scroll-mt-20 border-t border-neon/10">
                @include('mapas._defs')
                <div x-data="{ atual: 0, zoom: false, total: {{ count($mapas) }}, ir(i) { this.atual = (i + this.total) % this.total } }"
                    @keydown.escape.window="zoom = false"
                    @keydown.left.window="if ($el.getBoundingClientRect().top < innerHeight && $el.getBoundingClientRect().bottom > 0) ir(atual - 1)"
                    @keydown.right.window="if ($el.getBoundingClientRect().top < innerHeight && $el.getBoundingClientRect().bottom > 0) ir(atual + 1)"
                    class="mx-auto grid max-w-6xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
                    <div>
                        <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Onde</p>
                        <h2 class="titulo-secao mt-3">Mapa do evento</h2>
                        <p class="mt-4 text-zinc-600">{{ $endereco }}</p>

                        <ol class="mt-6 space-y-1.5">
                            @foreach ($mapas as $i => $m)
                                <li>
                                    <button type="button" @click="ir({{ $i }})"
                                        :class="atual === {{ $i }} ? 'border-neon/60 bg-neon/10 shadow-[0_0_18px_-8px_var(--tema-neon)]' : 'border-transparent hover:bg-zinc-100/60'"
                                        class="flex w-full items-start gap-3 rounded-lg border px-3 py-2.5 text-left">
                                        <span :class="atual === {{ $i }} ? 'border-destaque bg-destaque text-white shadow-[0_0_12px_-2px_var(--tema-neon)]' : 'border-neon/50 text-zinc-900'"
                                            class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md border font-mono text-xs">{{ $i + 1 }}</span>
                                        <span class="min-w-0">
                                            <span class="block font-display text-sm font-semibold text-zinc-950">{{ $m['titulo'] }}</span>
                                            @if ($m['detalhe'])<span class="block text-xs text-zinc-500">{{ $m['detalhe'] }}</span>@endif
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ol>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($endereco) }}" target="_blank" rel="noopener" class="btn-outline mt-6 px-5 py-2.5 text-xs">Abrir no Google Maps</a>
                    </div>

                    {{-- Visualizador --}}
                    <div class="glass-card self-start p-3 sm:p-4">
                        <div class="group relative overflow-hidden rounded-lg bg-black ring-1 ring-neon/20">
                            <div class="grid">
                                @foreach ($mapas as $i => $m)
                                    <div x-show="atual === {{ $i }}" x-transition.opacity.duration.250ms @if ($i > 0) x-cloak @endif
                                        class="col-start-1 row-start-1 aspect-[60/57] w-full">
                                        @include('mapas._mostrar', ['m' => $m])
                                    </div>
                                @endforeach
                            </div>
                            {{-- Legenda sobre a imagem --}}
                            <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent pt-10 pr-28 pb-3 pl-4">
                                @foreach ($mapas as $i => $m)
                                    <div x-show="atual === {{ $i }}" @if ($i > 0) x-cloak @endif>
                                        <p class="font-display text-base font-semibold text-white">{{ $m['titulo'] }}</p>
                                        @if ($m['detalhe'])<p class="text-xs text-white/75">{{ $m['detalhe'] }}</p>@endif
                                    </div>
                                @endforeach
                            </div>
                            <span class="absolute top-3 left-3 rounded-md border border-neon/40 bg-black/75 px-2 py-1 font-mono text-[11px] text-neon-soft backdrop-blur">
                                <span x-text="String(atual + 1).padStart(2, '0')">01</span> / {{ sprintf('%02d', count($mapas)) }}
                            </span>
                            <button type="button" @click="zoom = true"
                                class="absolute right-3 bottom-4 flex items-center gap-1.5 rounded-md border border-neon/40 bg-black/75 px-2.5 py-1 font-mono text-[11px] text-white uppercase backdrop-blur transition hover:border-neon">
                                <x-icon name="search" class="size-3.5" /> Ampliar
                            </button>
                            <button type="button" @click="ir(atual - 1)" aria-label="Mapa anterior"
                                class="absolute top-1/2 left-3 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-neon/50 bg-black/75 text-white backdrop-blur transition hover:border-neon hover:shadow-[0_0_16px_-4px_var(--tema-neon)] sm:opacity-0 sm:group-hover:opacity-100">
                                <x-icon name="arrow-left" class="size-5" />
                            </button>
                            <button type="button" @click="ir(atual + 1)" aria-label="Próximo mapa"
                                class="absolute top-1/2 right-3 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-neon/50 bg-black/75 text-white backdrop-blur transition hover:border-neon hover:shadow-[0_0_16px_-4px_var(--tema-neon)] sm:opacity-0 sm:group-hover:opacity-100">
                                <x-icon name="arrow-right" class="size-5" />
                            </button>
                        </div>

                        {{-- Miniaturas --}}
                        <div class="mt-3 grid gap-2" style="grid-template-columns: repeat({{ count($mapas) }}, minmax(0, 1fr))">
                            @foreach ($mapas as $i => $m)
                                <button type="button" @click="ir({{ $i }})" aria-label="Ver mapa: {{ $m['titulo'] }}"
                                    :class="atual === {{ $i }} ? 'ring-2 ring-neon opacity-100 shadow-[0_0_14px_-4px_var(--tema-neon)]' : 'ring-1 ring-neon/15 opacity-50 hover:opacity-90'"
                                    class="aspect-square overflow-hidden rounded-md bg-black">
                                    <span class="pointer-events-none block h-full w-full [&>svg]:h-full [&>svg]:w-full" aria-hidden="true">
                                        @include('mapas._mostrar', ['m' => $m])
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tela cheia (Ampliar) --}}
                    <div x-show="zoom" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex flex-col bg-black/95 p-3 backdrop-blur sm:p-6" role="dialog" aria-modal="true" aria-label="Mapa ampliado">
                        <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 pb-3">
                            @foreach ($mapas as $i => $m)
                                <div x-show="atual === {{ $i }}" class="min-w-0">
                                    <p class="truncate font-display text-lg font-semibold text-white">{{ $m['titulo'] }}</p>
                                    <p class="truncate text-sm text-white/70">{{ $m['detalhe'] }}</p>
                                </div>
                            @endforeach
                            <div class="flex shrink-0 items-center gap-2">
                                <span class="font-mono text-xs text-neon-soft"><span x-text="String(atual + 1).padStart(2, '0')">01</span> / {{ sprintf('%02d', count($mapas)) }}</span>
                                <button type="button" @click="ir(atual - 1)" aria-label="Mapa anterior" class="flex size-10 items-center justify-center rounded-full border border-neon/50 text-white hover:border-neon"><x-icon name="arrow-left" class="size-5" /></button>
                                <button type="button" @click="ir(atual + 1)" aria-label="Próximo mapa" class="flex size-10 items-center justify-center rounded-full border border-neon/50 text-white hover:border-neon"><x-icon name="arrow-right" class="size-5" /></button>
                                <button type="button" @click="zoom = false" aria-label="Fechar" class="flex size-10 items-center justify-center rounded-full border border-neon bg-destaque text-white"><x-icon name="x" class="size-5" /></button>
                            </div>
                        </div>
                        <div class="flex min-h-0 flex-1 items-center justify-center" @click.self="zoom = false">
                            @foreach ($mapas as $i => $m)
                                <template x-if="zoom && atual === {{ $i }}">
                                    <div class="aspect-[60/57] max-h-full w-full max-w-[min(100%,calc((100vh-8rem)*60/57))] overflow-hidden rounded-lg ring-1 ring-neon/30">
                                        @include('mapas._mostrar', ['m' => $m, 'classe' => 'h-full w-full object-contain'])
                                    </div>
                                </template>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @else
        <section id="mapa" class="scroll-mt-20 border-t border-neon/10">
            <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2">
                <div>
                    <p class="rotulo-secao">{{ sprintf('%02d', $n) }} // Onde</p>
                    <h2 class="titulo-secao mt-3">Mapa do evento</h2>
                    <p class="mt-4 text-zinc-600">{{ $endereco }}</p>
                    <ol class="mt-6 space-y-3">
                        @foreach ($pontos as $i => $ponto)
                            <li class="flex items-center gap-3 text-sm text-zinc-900">
                                <span class="flex size-6 shrink-0 items-center justify-center rounded-md border font-mono text-xs {{ $i === 0 ? 'border-destaque bg-destaque text-white shadow-[0_0_12px_-2px_var(--tema-neon)]' : 'border-neon/50 text-zinc-900' }}">{{ $i + 1 }}</span>
                                {{ $ponto }}
                            </li>
                        @endforeach
                    </ol>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($endereco) }}" target="_blank" rel="noopener" class="btn-outline mt-7 px-5 py-2.5 text-xs">Abrir no mapa</a>
                </div>

                <div class="glass-card bg-circuit-grid relative h-72 overflow-hidden sm:h-80" aria-hidden="true">
                    <div class="absolute top-1/2 right-[8%] left-[8%] border-t border-dashed border-neon/50"></div>
                    <div class="absolute top-[12%] bottom-[12%] left-1/2 border-l border-dashed border-neon/50"></div>
                    @foreach (array_slice($pontos, 0, 4) as $i => $ponto)
                        <div class="absolute flex items-center justify-center rounded-lg border font-display text-lg {{ $posicoes[$i] }} {{ $i === 0 ? 'border-neon bg-destaque/20 text-zinc-950 shadow-[0_0_24px_-6px_var(--tema-neon)]' : 'border-neon/40 bg-superficie text-zinc-900' }}">{{ $i + 1 }}</div>
                    @endforeach
                    <span class="absolute right-3 bottom-2 font-mono text-[9px] tracking-[0.2em] text-zinc-500 uppercase">Mapa esquemático</span>
                </div>
            </div>
        </section>
        @endif

        {{-- ============ PATROCINADORES (banco) ============ --}}
        @if ($edicao->patrocinios->isNotEmpty())
            <section class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3 border-y border-neon/10 py-7">
                    <span class="font-mono text-[10px] tracking-[0.25em] text-zinc-500 uppercase">Apoio</span>
                    @foreach ($edicao->patrocinios as $p)
                        <span class="font-display text-base font-semibold text-zinc-700">{{ $p->nome_patrocinador }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============ CHAMADA FINAL ============ --}}
        @if ($aberta)
            <section class="mx-auto max-w-6xl px-4 pt-16 sm:px-6">
                <div class="glass-card relative flex flex-col gap-8 overflow-hidden p-8 sm:p-12 md:flex-row md:items-center md:justify-between">
                    <div class="pointer-events-none absolute -top-24 -right-24 size-80 rounded-full bg-neon/25 blur-3xl"></div>
                    <div class="relative">
                        <h2 class="font-display text-3xl leading-tight font-bold text-zinc-950 uppercase sm:text-4xl">
                            Garanta sua vaga na<br><span class="text-glow">{{ config('identidade.sigla') }} {{ $edicao->data_inicio->year }}</span>
                        </h2>
                        <p class="mt-3 text-zinc-600">Inscrições abertas até o início de cada atividade. Vagas de minicursos limitadas.</p>
                    </div>
                    <a href="{{ $urlInscricao }}" class="btn-neon relative shrink-0 px-8 py-4">Inscreva-se agora</a>
                </div>
            </section>
        @endif
    @endif
</x-layouts.portal>
