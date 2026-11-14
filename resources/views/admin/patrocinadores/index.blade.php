<x-layouts.admin titulo="Patrocinadores">
    <x-cabecalho titulo="Patrocinadores" subtitulo="Empresas e instituições que apoiam o evento.">
        @can('cadastros-globais')
            <x-button :href="route('admin.patrocinadores.create')" icone="plus">Novo patrocinador</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome ou observação...">
        <x-filtro-select name="situacao" :opcoes="['com' => 'Com patrocínios', 'sem' => 'Sem patrocínios']" todos="Todos" />
    </x-filtros>

    <x-table :vazia="$patrocinadores->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="nome">Nome</x-th>
            <x-th>Observações</x-th>
            <x-th coluna="patrocinios" class="text-right">Edições apoiadas</x-th>
            <x-th coluna="total" class="text-right">Total em R$</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($patrocinadores as $p)
            <tr>
                <td class="font-medium"><a href="{{ route('admin.patrocinadores.show', $p) }}" class="link">{{ $p->nome }}</a></td>
                <td class="max-w-md truncate text-zinc-500">{{ $p->observacoes ?? '—' }}</td>
                <td class="text-right tabular-nums">{{ $p->patrocinios_count }}</td>
                <td class="text-right tabular-nums">{{ formata_moeda($p->total_financeiro) }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.patrocinadores.show', $p)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.patrocinadores.edit', $p)" title="Editar" />
                        <x-excluir :action="route('admin.patrocinadores.destroy', $p)" :descricao="$p->nome" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $patrocinadores->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
