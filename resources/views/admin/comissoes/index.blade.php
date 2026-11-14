<x-layouts.admin titulo="Edições">
    <x-cabecalho titulo="Edições" subtitulo="Cada edição tem sua comissão organizadora, presidente e vice.">
        @can('create', \App\Models\Comissao::class)
            <x-button :href="route('admin.comissoes.create')" icone="plus">Nova edição</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome, local ou observação...">
        <x-filtro-select name="ano" :opcoes="$anos" todos="Todos os anos" class="lg:w-36" />
    </x-filtros>

    <x-table :vazia="$comissoes->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="edicao">Edição</x-th>
            <x-th coluna="inicio">Período</x-th>
            <x-th coluna="local">Local sede</x-th>
            <x-th>Presidência</x-th>
            <x-th class="text-right">Coord.</x-th>
            <x-th coluna="atividades" class="text-right">Ativ.</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($comissoes as $c)
            <tr @class(['bg-brand-50/50' => $edicaoAtual?->id_comissao === $c->id_comissao])>
                <td class="whitespace-nowrap">
                    <a href="{{ route('admin.comissoes.show', $c) }}" class="link">{{ $c->titulo }}</a>
                    @if ($edicaoAtual?->id_comissao === $c->id_comissao)<x-badge cor="blue" class="ml-1">Selecionada</x-badge>@endif
                </td>
                <td class="whitespace-nowrap">{{ $c->periodo }}</td>
                <td class="max-w-56 truncate text-zinc-600" title="{{ $c->local_sede }}">{{ $c->local_sede }}</td>
                <td class="text-xs">
                    <span class="block"><span class="text-zinc-400">P:</span> {{ $c->presidencia?->presidente?->nome_completo ?? '—' }}</span>
                    <span class="block"><span class="text-zinc-400">V:</span> {{ $c->presidencia?->vice?->nome_completo ?? '—' }}</span>
                </td>
                <td class="text-right tabular-nums">{{ $c->coordenadorias_count }}</td>
                <td class="text-right tabular-nums">{{ $c->atividades_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.comissoes.show', $c)" title="Detalhes" />
                    @can('update', $c)
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.comissoes.edit', $c)" title="Editar" />
                        <x-excluir :action="route('admin.comissoes.destroy', $c)" :descricao="'a '.$c->titulo" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $comissoes->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
