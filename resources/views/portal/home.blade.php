<x-layouts.portal>
    @if (! $edicao)
        <section class="mx-auto max-w-3xl px-4 py-24 text-center">
            <h1 class="text-3xl font-semibold text-zinc-900">Semana Acadêmica de Computação</h1>
            <p class="mt-3 text-zinc-500">Nenhuma edição cadastrada ainda.</p>
        </section>
    @else
        {{-- Destaque da edição mais recente --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 text-white">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, #fff 0, transparent 35%), radial-gradient(circle at 80% 60%, #93bbfd 0, transparent 40%)"></div>
            <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium tracking-wide uppercase ring-1 ring-white/20">
                    {{ $edicao->edicao }}ª edição · {{ $edicao->data_inicio->year }}
                </p>
                <h1 class="mt-5 max-w-3xl text-3xl leading-tight font-semibold tracking-tight sm:text-5xl">{{ $edicao->nome_evento }}</h1>
                <div class="mt-6 flex flex-col gap-3 text-brand-100 sm:flex-row sm:gap-8">
                    <span class="flex items-center gap-2"><x-icon name="calendar" class="size-5" /> {{ $edicao->data_inicio->translatedFormat('d') }} a {{ $edicao->data_termino->translatedFormat('d \d\e F \d\e Y') }}</span>
                    <span class="flex items-center gap-2"><x-icon name="map" class="size-5" /> {{ $edicao->local_sede }}</span>
                </div>
                @if ($edicao->observacoes)
                    <p class="mt-4 max-w-2xl text-sm text-brand-200">{{ $edicao->observacoes }}</p>
                @endif
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('portal.programacao') }}" class="rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-brand-800 shadow hover:bg-brand-50">Ver programação</a>
                    @guest('participante')
                        <a href="{{ route('portal.cadastro') }}" class="rounded-lg px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/10">Criar minha conta</a>
                    @else
                        <a href="{{ route('portal.inscricoes') }}" class="rounded-lg px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/10">Minhas inscrições</a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- Números --}}
        <section class="mx-auto -mt-8 max-w-6xl px-4 sm:px-6">
            <div class="relative grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-zinc-200 bg-zinc-200 shadow-sm sm:grid-cols-4">
                @foreach (['Atividades' => $totais['atividades'], 'Ministrantes' => $totais['ministrantes'], 'Vagas' => $totais['vagas'], 'Dias de evento' => $totais['dias']] as $rotulo => $valor)
                    <div class="bg-white px-5 py-5 text-center">
                        <p class="text-2xl font-semibold text-zinc-900 tabular-nums sm:text-3xl">{{ $valor }}</p>
                        <p class="text-xs text-zinc-500 sm:text-sm">{{ $rotulo }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Destaques da programação --}}
        <section class="mx-auto max-w-6xl px-4 pt-14 sm:px-6">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-900">Destaques da programação</h2>
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
                            <p class="mb-1.5 text-xs font-medium text-zinc-500 uppercase">{{ $atividade->data_realizacao->translatedFormat('l, d/m') }}</p>
                            @include('portal._cartao-atividade', ['atividade' => $atividade])
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Patrocinadores --}}
        <section class="mx-auto max-w-6xl px-4 pt-14 sm:px-6">
            <h2 class="text-xl font-semibold text-zinc-900">Patrocinadores</h2>
            <p class="text-sm text-zinc-500">Quem torna esta edição possível.</p>
            @if ($edicao->patrocinios->isEmpty())
                <p class="mt-4 text-sm text-zinc-500">Nenhum patrocinador confirmado ainda.</p>
            @else
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($edicao->patrocinios as $p)
                        <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                            <p class="font-semibold text-zinc-800">{{ $p->nome_patrocinador }}</p>
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
