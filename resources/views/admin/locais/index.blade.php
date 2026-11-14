<x-layouts.admin titulo="Locais">
    <x-cabecalho titulo="Locais" subtitulo="Auditórios, laboratórios e salas usados nas atividades.">
        @can('cadastros-globais')
            <x-button :href="route('admin.locais.create')" icone="plus">Novo local</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por faculdade, bloco ou número...">
        <x-filtro-select name="tipo" :opcoes="$tipos" todos="Todos os tipos" />
        <x-filtro-select name="faculdade" :opcoes="$faculdades" todos="Todas as faculdades" class="lg:w-72" />
    </x-filtros>

    <x-table :vazia="$locais->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="local">Local</x-th>
            <x-th coluna="tipo">Tipo</x-th>
            <x-th coluna="faculdade">Faculdade</x-th>
            <x-th coluna="capacidade" class="text-right">Capacidade</x-th>
            <x-th class="text-right">Equip.</x-th>
            <x-th coluna="atividades" class="text-right">Atividades</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($locais as $local)
            <tr>
                <td class="font-medium whitespace-nowrap"><a href="{{ route('admin.locais.show', $local) }}" class="link">{{ $local->nome_curto }}</a></td>
                <td><x-badge :enum="$local->tipo" /></td>
                <td class="text-zinc-600">{{ $local->faculdade }}</td>
                <td class="text-right tabular-nums">{{ $local->capacidade }}</td>
                <td class="text-right tabular-nums">{{ $local->equipamentos_count }}</td>
                <td class="text-right tabular-nums">{{ $local->atividades_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.locais.show', $local)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.locais.edit', $local)" title="Editar" />
                        <x-excluir :action="route('admin.locais.destroy', $local)" :descricao="'o local '.$local->nome_curto" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $locais->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
