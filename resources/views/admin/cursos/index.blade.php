<x-layouts.admin titulo="Cursos">
    <x-cabecalho titulo="Cursos" subtitulo="Cursos de graduação dos integrantes da comissão.">
        @can('cadastros-globais')
            <x-button :href="route('admin.cursos.create')" icone="plus">Novo curso</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por curso ou faculdade...">
        <x-filtro-select name="faculdade" :opcoes="$faculdades" todos="Todas as faculdades" class="lg:w-72" />
    </x-filtros>

    <x-table :vazia="$cursos->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="nome">Curso</x-th>
            <x-th coluna="faculdade">Faculdade</x-th>
            <x-th coluna="integrantes" class="text-right">Integrantes</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($cursos as $curso)
            <tr>
                <td><a href="{{ route('admin.cursos.show', $curso) }}" class="link">{{ $curso->nome_curso }}</a></td>
                <td>{{ $curso->faculdade }}</td>
                <td class="text-right tabular-nums">{{ $curso->integrantes_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.cursos.show', $curso)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.cursos.edit', $curso)" title="Editar" />
                        <x-excluir :action="route('admin.cursos.destroy', $curso)" :descricao="'o curso '.$curso->nome_curso" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $cursos->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
