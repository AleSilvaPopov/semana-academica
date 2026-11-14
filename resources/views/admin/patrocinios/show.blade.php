<x-layouts.admin titulo="Patrocínio">
    @php $chaves = ['comissao' => $comissao, 'patrocinador' => $patrocinador]; @endphp
    <x-cabecalho :titulo="'Patrocínio: '.$patrocinio->nome_patrocinador" :subtitulo="$comissao->titulo" :voltar="route('admin.patrocinios.index')">
        @can('gerir-edicao', $comissao->id_comissao)
            <x-button variante="secundario" icone="pencil" :href="route('admin.patrocinios.edit', $chaves)">Editar</x-button>
            <x-excluir :action="route('admin.patrocinios.destroy', $chaves)" :descricao="'o patrocínio de '.$patrocinio->nome_patrocinador" rotulo="Remover" tamanho="md" texto="Remover" />
        @endcan
    </x-cabecalho>
    <x-secao class="max-w-3xl">
        <dl class="divide-y divide-zinc-100">
            <x-detalhe rotulo="Patrocinador"><a href="{{ route('admin.patrocinadores.show', $patrocinador) }}" class="link">{{ $patrocinador->nome }}</a></x-detalhe>
            <x-detalhe rotulo="Edição"><a href="{{ route('admin.comissoes.show', $comissao) }}" class="link">{{ $comissao->nome_completo }}</a></x-detalhe>
            <x-detalhe rotulo="Tipo"><x-badge :enum="$patrocinio->tipo_patrocinio" /></x-detalhe>
            <x-detalhe rotulo="Descrição">{{ $patrocinio->descricao_patrocinio }}</x-detalhe>
            <x-detalhe rotulo="Valor">{{ formata_moeda($patrocinio->valor) }}</x-detalhe>
            <x-detalhe rotulo="Requisitos">{{ $patrocinio->requisitos ?? '—' }}</x-detalhe>
        </dl>
    </x-secao>
</x-layouts.admin>
