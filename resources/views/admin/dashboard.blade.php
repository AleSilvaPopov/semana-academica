<x-layouts.admin titulo="Dashboard">
    <x-cabecalho :titulo="'Dashboard · '.$edicao->titulo" :subtitulo="$edicao->periodo.' · '.$edicao->local_sede">
        <x-button variante="secundario" icone="eye" :href="route('admin.comissoes.show', $edicao)">Detalhes da edição</x-button>
    </x-cabecalho>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-estatistica titulo="Atividades" :valor="$atividades->count()" icone="presentation" :detalhe="$porStatus['confirmada'].' confirmada(s) · '.$porStatus['realizada'].' realizada(s)'" />
        <x-estatistica titulo="Inscrições" :valor="$totalInscricoes" icone="ticket" :detalhe="$participantesUnicos.' participante(s) · '.$vagasTotais.' vagas no total'" />
        <x-estatistica titulo="Taxa de presença" :valor="$taxaPresenca !== null ? number_format($taxaPresenca, 1, ',', '.').'%' : '—'" icone="check" cor="green" detalhe="nas atividades realizadas" />
        <x-estatistica titulo="Tarefas em aberto" :valor="$tarefasAbertas" icone="clipboard" :cor="$tarefasAtrasadas->isNotEmpty() ? 'red' : 'amber'" :detalhe="$tarefasAtrasadas->count().' atrasada(s)'" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Ocupação de vagas por atividade --}}
        <x-secao titulo="Ocupação de vagas por atividade" class="xl:col-span-2">
            <x-slot:acoes><a href="{{ route('admin.atividades.index') }}" class="link text-xs">Ver atividades</a></x-slot:acoes>
            @forelse ($atividades as $a)
                <div class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 border-b border-zinc-100 py-2.5 last:border-0 sm:grid-cols-[minmax(0,1fr)_12rem_4.5rem]">
                    <div class="min-w-0">
                        <a href="{{ route('admin.inscricoes.show', $a) }}" class="block truncate text-sm font-medium text-zinc-800 hover:underline">{{ $a->nome }}</a>
                        <p class="text-xs text-zinc-500">{{ formata_data_hora($a->data_realizacao) }} · <x-badge :enum="$a->status" class="!py-0" /></p>
                    </div>
                    <x-barra-progresso :valor="$a->inscricoes_count" :total="$a->vagas" class="order-3 col-span-2 sm:order-none sm:col-span-1" />
                    <span class="text-right text-sm tabular-nums text-zinc-700">{{ $a->inscricoes_count }}/{{ $a->vagas }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-500">Nenhuma atividade cadastrada nesta edição.</p>
            @endforelse
        </x-secao>

        <div class="space-y-6">
            <x-secao titulo="Atividades por status">
                <div class="space-y-2.5">
                    @foreach (\App\Enums\StatusAtividade::cases() as $s)
                        @php $qtd = $porStatus[$s->value]; $max = max(1, $porStatus->max()); @endphp
                        <div class="flex items-center gap-3">
                            <x-badge :enum="$s" class="w-24 justify-center" />
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full bg-brand-500" style="width: {{ $qtd / $max * 100 }}%"></div>
                            </div>
                            <span class="w-6 text-right text-sm font-medium tabular-nums">{{ $qtd }}</span>
                        </div>
                    @endforeach
                </div>
            </x-secao>

            <x-secao titulo="Patrocínios por tipo">
                <x-slot:acoes><a href="{{ route('admin.patrocinios.index') }}" class="link text-xs">Ver todos</a></x-slot:acoes>
                <dl class="space-y-2 text-sm">
                    @foreach ($tiposPatrocinio as $tipo)
                        @php $l = $patrocinios[$tipo->value] ?? null; @endphp
                        <div class="flex items-center justify-between">
                            <dt class="flex items-center gap-2"><x-badge :enum="$tipo" /> <span class="text-xs text-zinc-500">{{ $l?->quantidade ?? 0 }}</span></dt>
                            <dd class="tabular-nums">{{ $tipo === \App\Enums\TipoPatrocinio::Financeiro ? formata_moeda($l?->soma ?? 0) : ($l?->quantidade ? $l->quantidade.' item(ns)' : '—') }}</dd>
                        </div>
                    @endforeach
                    <div class="flex justify-between border-t border-zinc-100 pt-2 font-semibold">
                        <dt>Total em dinheiro</dt><dd class="tabular-nums">{{ formata_moeda($totalFinanceiro) }}</dd>
                    </div>
                </dl>
            </x-secao>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-secao titulo="Próximas atividades">
            @forelse ($proximas as $a)
                <div class="flex items-center gap-3 border-b border-zinc-100 py-2.5 last:border-0">
                    <div class="w-14 shrink-0 rounded-lg bg-brand-50 py-1 text-center">
                        <p class="text-lg leading-none font-semibold text-brand-700">{{ $a->data_realizacao->format('d') }}</p>
                        <p class="text-[10px] font-medium text-brand-600 uppercase">{{ $a->data_realizacao->translatedFormat('M') }}</p>
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('admin.atividades.show', $a) }}" class="block truncate text-sm font-medium text-zinc-800 hover:underline">{{ $a->nome }}</a>
                        <p class="text-xs text-zinc-500">{{ $a->data_realizacao->format('H:i') }} · {{ $a->local?->nome_curto }} · {{ $a->vagas_restantes }} vaga(s)</p>
                    </div>
                    <x-badge :enum="$a->tipo" />
                </div>
            @empty
                <p class="text-sm text-zinc-500">Nenhuma atividade futura nesta edição.</p>
            @endforelse
        </x-secao>

        <x-secao titulo="Tarefas atrasadas" :descricao="'Pendentes: '.($tarefasPorStatus['pendente'] ?? 0).' · Em andamento: '.($tarefasPorStatus['em_andamento'] ?? 0).' · Concluídas: '.($tarefasPorStatus['concluida'] ?? 0)">
            <x-slot:acoes><a href="{{ route('admin.tarefas.index', ['prazo' => 'atrasadas']) }}" class="link text-xs">Ver todas</a></x-slot:acoes>
            @forelse ($tarefasAtrasadas->take(6) as $t)
                <div class="flex items-start justify-between gap-3 border-b border-zinc-100 py-2.5 last:border-0">
                    <div class="min-w-0">
                        <a href="{{ route('admin.tarefas.show', $t) }}" class="block text-sm text-zinc-800 hover:underline">{{ \Illuminate\Support\Str::limit($t->descricao, 80) }}</a>
                        <p class="text-xs text-zinc-500">{{ $t->responsaveis->pluck('nome_completo')->join(', ') ?: 'Sem responsável' }}</p>
                    </div>
                    <span class="shrink-0 text-xs font-semibold text-red-700">venceu {{ formata_data($t->prazo_final) }}</span>
                </div>
            @empty
                <p class="flex items-center gap-2 text-sm text-emerald-700"><x-icon name="check" class="size-5" /> Nenhuma tarefa atrasada.</p>
            @endforelse
        </x-secao>
    </div>
</x-layouts.admin>
