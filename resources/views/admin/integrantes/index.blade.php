<x-layouts.admin titulo="Integrantes">
    <x-cabecalho titulo="Integrantes" subtitulo="Membros da comissão organizadora (todas as edições).">
        @can('cadastros-globais')
            <x-button :href="route('admin.integrantes.create')" icone="plus">Novo integrante</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome, RGA, CPF ou função...">
        <x-filtro-select name="curso" :opcoes="$cursos" todos="Todos os cursos" />
        <x-filtro-select name="coordenadoria" :opcoes="$coordenadorias" todos="Atua em (edição atual)" />
        <x-filtro-select name="papel" :opcoes="['coordenadores' => 'Coordenadores da edição', 'sem_coordenadoria' => 'Sem coordenadoria']" todos="Qualquer papel" />
    </x-filtros>

    <x-table :vazia="$integrantes->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="nome">Nome</x-th>
            <x-th coluna="rga">RGA</x-th>
            <x-th>Curso</x-th>
            <x-th coluna="funcao">Função</x-th>
            <x-th>Atua em</x-th>
            <x-th coluna="ingresso">Ingresso</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($integrantes as $i)
            <tr>
                <td>
                    <a href="{{ route('admin.integrantes.show', $i) }}" class="link">{{ $i->nome_completo }}</a>
                    @if ($i->coordenadorias_que_coordena_count)<x-badge cor="purple" class="ml-1">Coordenou {{ $i->coordenadorias_que_coordena_count }}x</x-badge>@endif
                </td>
                <td class="tabular-nums">{{ $i->rga }}</td>
                <td class="text-zinc-600">{{ $i->curso?->nome_curso }}</td>
                <td class="text-zinc-600">{{ $i->funcao }}</td>
                <td>
                    @if ($i->coordenadoria)
                        <x-badge cor="blue">{{ $i->coordenadoria->nome }} · {{ $i->coordenadoria->comissao?->edicao }}ª</x-badge>
                    @else
                        <span class="text-zinc-400">—</span>
                    @endif
                </td>
                <td class="whitespace-nowrap">{{ formata_data($i->data_ingresso) }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.integrantes.show', $i)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.integrantes.edit', $i)" title="Editar" />
                        <x-excluir :action="route('admin.integrantes.destroy', $i)" :descricao="$i->nome_completo" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $integrantes->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
