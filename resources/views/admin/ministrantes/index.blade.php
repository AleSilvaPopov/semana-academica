<x-layouts.admin titulo="Ministrantes">
    <x-cabecalho titulo="Ministrantes" subtitulo="Participantes que ministram palestras, minicursos e workshops.">
        @can('cadastros-globais')
            <x-button :href="route('admin.ministrantes.create')" icone="plus">Novo ministrante</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome, instituição ou CPF...">
        <x-filtro-select name="foto" :opcoes="['com' => 'Com foto', 'sem' => 'Sem foto']" todos="Com ou sem foto" />
    </x-filtros>

    <x-table :vazia="$ministrantes->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="nome">Ministrante</x-th>
            <x-th coluna="instituicao">Instituição</x-th>
            <x-th class="text-right">Certificados</x-th>
            <x-th coluna="atividades" class="text-right">Atividades</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($ministrantes as $m)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-avatar :nome="$m->participante->nome_completo" :url="$m->imagem_url" tamanho="size-9" />
                        <div>
                            <a href="{{ route('admin.ministrantes.show', $m) }}" class="link">{{ $m->participante->nome_completo }}</a>
                            <p class="text-xs text-zinc-500">{{ formata_cpf($m->cpf) }}</p>
                        </div>
                    </div>
                </td>
                <td class="text-zinc-600">{{ $m->participante->instituicao_origem }}</td>
                <td class="text-right tabular-nums">{{ $m->certificados_count }}</td>
                <td class="text-right tabular-nums">{{ $m->atividades_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.ministrantes.show', $m)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.ministrantes.edit', $m)" title="Editar" />
                        <x-excluir :action="route('admin.ministrantes.destroy', $m)" :descricao="'o ministrante '.$m->participante->nome_completo" rotulo="Remover" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $ministrantes->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
