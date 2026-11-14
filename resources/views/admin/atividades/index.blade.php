<x-layouts.admin titulo="Atividades">
    <x-cabecalho titulo="Atividades" :subtitulo="'Edição selecionada: '.$edicaoAtual?->titulo">
        @can('create', \App\Models\Atividade::class)
            <x-button :href="route('admin.atividades.create')" icone="plus">Nova atividade</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome ou descrição...">
        <x-filtro-select name="tipo" :opcoes="$tipos" todos="Todos os tipos" class="lg:w-36" />
        <x-filtro-select name="status" :opcoes="$status" todos="Todos os status" class="lg:w-36" />
        <x-filtro-select name="coordenadoria" :opcoes="$coordenadorias" todos="Todas as coordenadorias" />
        <x-filtro-select name="dia" :opcoes="$dias" todos="Todos os dias" class="lg:w-36" />
    </x-filtros>

    <x-table :vazia="$atividades->isEmpty()" vazio="Nenhuma atividade encontrada nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="nome">Atividade</x-th>
            <x-th coluna="data">Data e hora</x-th>
            <x-th>Local</x-th>
            <x-th>Status</x-th>
            <x-th coluna="inscritos">Inscritos/vagas</x-th>
            <x-th coluna="valor" class="text-right">Valor</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($atividades as $a)
            <tr>
                <td>
                    <a href="{{ route('admin.atividades.show', $a) }}" class="link">{{ $a->nome }}</a>
                    <div class="mt-0.5 flex flex-wrap items-center gap-1.5 text-xs text-zinc-500">
                        <x-badge :enum="$a->tipo" /> {{ $a->coordenadoria?->nome }} · {{ formata_duracao($a->duracao_minutos) }}
                    </div>
                    @if ($a->ministrantes->isNotEmpty())
                        <p class="mt-0.5 text-xs text-zinc-500">{{ $a->ministrantes->map(fn ($m) => $m->participante->nome_completo)->join(', ') }}</p>
                    @endif
                </td>
                <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                <td class="whitespace-nowrap">{{ $a->local?->nome_curto }}</td>
                <td><x-badge :enum="$a->status" /></td>
                <td class="min-w-36">
                    <span class="text-sm font-medium tabular-nums">{{ $a->inscricoes_count }}/{{ $a->vagas }}</span>
                    <x-barra-progresso :valor="$a->inscricoes_count" :total="$a->vagas" class="mt-1" />
                </td>
                <td class="text-right whitespace-nowrap tabular-nums">{{ (float) $a->valor_inscricao > 0 ? formata_moeda($a->valor_inscricao) : 'Gratuita' }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.atividades.show', $a)" title="Detalhes" />
                    <x-button variante="fantasma" tamanho="sm" icone="ticket" :href="route('admin.inscricoes.show', $a)" title="Inscrições e presença" />
                    @can('update', $a)
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.atividades.edit', $a)" title="Editar" />
                        <x-excluir :action="route('admin.atividades.destroy', $a)" :descricao="'a atividade '.$a->nome" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $atividades->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
