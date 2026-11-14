<x-layouts.admin titulo="Tarefas">
    <x-cabecalho titulo="Tarefas" :subtitulo="'Edição selecionada: '.$edicaoAtual?->titulo">
        <x-button variante="secundario" icone="check" :href="route('admin.minhas-tarefas')">Minhas tarefas</x-button>
        @can('create', \App\Models\Tarefa::class)
            <x-button :href="route('admin.tarefas.create')" icone="plus">Nova tarefa</x-button>
        @endcan
    </x-cabecalho>

    @if ($atrasadas > 0)
        <x-alert tipo="aviso" class="mb-4">
            Há <strong>{{ $atrasadas }}</strong> tarefa(s) atrasada(s) nesta edição.
            <a href="{{ route('admin.tarefas.index', ['prazo' => 'atrasadas']) }}" class="font-semibold underline">Ver atrasadas</a>
        </x-alert>
    @endif

    <x-filtros placeholder="Buscar na descrição...">
        <x-filtro-select name="status" :opcoes="$statusOpcoes" todos="Todos os status" class="lg:w-40" />
        <x-filtro-select name="prazo" :opcoes="['atrasadas' => 'Atrasadas', 'semana' => 'Vencem em 7 dias', 'em_dia' => 'Em dia']" todos="Qualquer prazo" class="lg:w-44" />
        <x-filtro-select name="criador" :opcoes="$criadores" todos="Qualquer criador" />
    </x-filtros>

    <x-table :vazia="$tarefas->isEmpty()" vazio="Nenhuma tarefa encontrada nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="id">#</x-th>
            <x-th>Tarefa</x-th>
            <x-th>Responsáveis</x-th>
            <x-th coluna="criacao">Criada em</x-th>
            <x-th coluna="prazo">Prazo</x-th>
            <x-th coluna="status">Status</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($tarefas as $t)
            <tr @class(['bg-red-50/70 hover:bg-red-50' => $t->atrasada])>
                <td class="text-zinc-400 tabular-nums">{{ $t->id_tarefa }}</td>
                <td class="max-w-md">
                    <a href="{{ route('admin.tarefas.show', $t) }}" class="link">{{ $t->descricao }}</a>
                    <p class="text-xs text-zinc-500">Criada por {{ $t->criador?->nome_completo }}</p>
                </td>
                <td class="text-xs text-zinc-600">{{ $t->responsaveis->pluck('nome_completo')->join(', ') ?: '—' }}</td>
                <td class="whitespace-nowrap">{{ formata_data($t->data_criacao) }}</td>
                <td class="whitespace-nowrap {{ $t->atrasada ? 'font-semibold text-red-700' : '' }}">{{ formata_data($t->prazo_final) }}</td>
                <td class="whitespace-nowrap"><x-badge :enum="$t->status" />@if ($t->atrasada)<x-badge cor="red" class="ml-1">Atrasada</x-badge>@endif</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.tarefas.show', $t)" title="Detalhes" />
                    @can('update', $t)
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.tarefas.edit', $t)" title="Editar" />
                        <x-excluir :action="route('admin.tarefas.destroy', $t)" :descricao="'a tarefa #'.$t->id_tarefa" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $tarefas->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
