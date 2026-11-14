<x-layouts.admin titulo="Patrocínios">
    <x-cabecalho titulo="Patrocínios" :subtitulo="'Edição selecionada: '.$edicaoAtual?->titulo">
        @can('gerir-edicao')
            <x-button :href="route('admin.patrocinios.create')" icone="plus">Novo patrocínio</x-button>
        @endcan
    </x-cabecalho>

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach (\App\Enums\TipoPatrocinio::cases() as $tipo)
            @php $linha = $porTipo[$tipo->value] ?? null; @endphp
            <div class="cartao p-4">
                <x-badge :enum="$tipo" />
                <p class="mt-2 text-lg font-semibold tabular-nums text-zinc-900">{{ $tipo === \App\Enums\TipoPatrocinio::Financeiro ? formata_moeda($linha?->soma ?? 0) : ($linha?->quantidade ?? 0) }}</p>
                <p class="text-xs text-zinc-500">{{ $linha?->quantidade ?? 0 }} patrocínio(s)</p>
            </div>
        @endforeach
    </div>

    <x-filtros placeholder="Buscar por patrocinador, descrição ou requisito...">
        <x-filtro-select name="tipo" :opcoes="$tipos" todos="Todos os tipos" />
    </x-filtros>

    <x-table :vazia="$patrocinios->isEmpty()" vazio="Nenhum patrocínio nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="patrocinador">Patrocinador</x-th>
            <x-th coluna="tipo">Tipo</x-th>
            <x-th>Descrição</x-th>
            <x-th>Requisitos</x-th>
            <x-th coluna="valor" class="text-right">Valor</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($patrocinios as $p)
            @php $chaves = ['comissao' => $p->id_comissao, 'patrocinador' => $p->nome_patrocinador]; @endphp
            <tr>
                <td class="font-medium"><a href="{{ route('admin.patrocinios.show', $chaves) }}" class="link">{{ $p->nome_patrocinador }}</a></td>
                <td><x-badge :enum="$p->tipo_patrocinio" /></td>
                <td>{{ $p->descricao_patrocinio }}</td>
                <td class="max-w-56 truncate text-zinc-500" title="{{ $p->requisitos }}">{{ $p->requisitos ?? '—' }}</td>
                <td class="text-right whitespace-nowrap tabular-nums">{{ formata_moeda($p->valor) }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.patrocinios.show', $chaves)" title="Detalhes" />
                    @can('gerir-edicao', $p->id_comissao)
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.patrocinios.edit', $chaves)" title="Editar" />
                        <x-excluir :action="route('admin.patrocinios.destroy', $chaves)" :descricao="'o patrocínio de '.$p->nome_patrocinador" rotulo="Remover" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:rodape>
            <tr>
                <td colspan="4" class="px-4 py-3 text-right text-sm">Total financeiro {{ request('tipo') || request('q') ? '(filtro atual)' : 'da edição' }}</td>
                <td class="px-4 py-3 text-right text-sm whitespace-nowrap tabular-nums">{{ formata_moeda($total, 'R$ 0,00') }}</td>
                <td></td>
            </tr>
        </x-slot:rodape>
        <x-slot:paginacao>{{ $patrocinios->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
