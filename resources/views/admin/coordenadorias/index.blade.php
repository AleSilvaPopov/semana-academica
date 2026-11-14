<x-layouts.admin titulo="Coordenadorias">
    <x-cabecalho titulo="Coordenadorias" :subtitulo="'Edição selecionada: '.$edicaoAtual?->titulo">
        @can('create', \App\Models\Coordenadoria::class)
            <x-button :href="route('admin.coordenadorias.create')" icone="plus">Nova coordenadoria</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome ou atuação...">
        <x-filtro-select name="situacao" :opcoes="['ativa' => 'Coordenação vigente', 'encerrada' => 'Coordenação encerrada']" todos="Qualquer situação" class="lg:w-56" />
    </x-filtros>

    <x-table :vazia="$coordenadorias->isEmpty()" vazio="Nenhuma coordenadoria nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="nome">Coordenadoria</x-th>
            <x-th>Coordenador(a)</x-th>
            <x-th coluna="inicio">Coordenação</x-th>
            <x-th coluna="integrantes" class="text-right">Integrantes <span class="normal-case">(view)</span></x-th>
            <x-th coluna="atividades" class="text-right">Atividades</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($coordenadorias as $c)
            <tr>
                <td>
                    <a href="{{ route('admin.coordenadorias.show', $c) }}" class="link">{{ $c->nome }}</a>
                    <p class="max-w-md truncate text-xs text-zinc-500">{{ $c->descricao_atuacao }}</p>
                </td>
                <td>@if ($c->coordenador)<a href="{{ route('admin.integrantes.show', $c->coordenador) }}" class="text-zinc-700 hover:underline">{{ $c->coordenador->nome_completo }}</a>@endif</td>
                <td class="text-xs whitespace-nowrap">{{ formata_data($c->data_inicio_coordenacao) }} a {{ formata_data($c->data_termino_coordenacao, 'atual') }}</td>
                <td class="text-right tabular-nums">{{ $c->quantidade_integrantes ?? 0 }}</td>
                <td class="text-right tabular-nums">{{ $c->atividades_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.coordenadorias.show', $c)" title="Detalhes" />
                    @can('update', $c)
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.coordenadorias.edit', $c)" title="Editar" />
                        <x-excluir :action="route('admin.coordenadorias.destroy', $c)" :descricao="'a coordenadoria '.$c->nome" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $coordenadorias->links() }}</x-slot:paginacao>
    </x-table>
    <p class="mt-3 text-xs text-zinc-500">A coluna "Integrantes" vem da view <code>vw_quantidade_integrantes</code>. Como cada integrante guarda só a coordenadoria em que atua mais recentemente, edições antigas podem aparecer com 0.</p>
</x-layouts.admin>
