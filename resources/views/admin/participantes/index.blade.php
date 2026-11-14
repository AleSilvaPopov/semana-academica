<x-layouts.admin titulo="Participantes">
    <x-cabecalho titulo="Participantes" subtitulo="Público do evento (contas do portal).">
        @can('cadastros-globais')
            <x-button :href="route('admin.participantes.create')" icone="plus">Novo participante</x-button>
        @endcan
    </x-cabecalho>

    <x-filtros placeholder="Buscar por nome, e-mail ou CPF...">
        <x-filtro-select name="tipo" :opcoes="$tipos" todos="Todos os tipos" class="lg:w-40" />
        <x-filtro-select name="instituicao" :opcoes="$instituicoes" todos="Todas as instituições" class="lg:w-64" />
        <x-filtro-select name="status" :opcoes="$status" todos="Qualquer status" class="lg:w-40" />
    </x-filtros>

    <x-table :vazia="$participantes->isEmpty()">
        <x-slot:cabecalho>
            <x-th coluna="nome">Nome</x-th>
            <x-th>CPF</x-th>
            <x-th coluna="tipo">Tipo</x-th>
            <x-th coluna="instituicao">Instituição</x-th>
            <x-th>Conta</x-th>
            <x-th coluna="inscricoes" class="text-right">Inscrições</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($participantes as $p)
            <tr>
                <td>
                    <a href="{{ route('admin.participantes.show', $p) }}" class="link">{{ $p->nome_completo }}</a>
                    @if ($p->ministrante)<x-badge cor="purple" class="ml-1">Ministrante</x-badge>@endif
                    <p class="text-xs text-zinc-500">{{ $p->email }}</p>
                </td>
                <td class="whitespace-nowrap tabular-nums">{{ formata_cpf($p->cpf) }}</td>
                <td><x-badge :enum="$p->tipo_participante" /></td>
                <td class="max-w-56 truncate text-zinc-600" title="{{ $p->instituicao_origem }}">{{ $p->instituicao_origem }}</td>
                <td><x-badge :enum="$p->status_conta" /></td>
                <td class="text-right tabular-nums">{{ $p->inscricoes_count }}</td>
                <td class="text-right whitespace-nowrap">
                    <x-button variante="fantasma" tamanho="sm" icone="eye" :href="route('admin.participantes.show', $p)" title="Detalhes" />
                    @can('cadastros-globais')
                        <x-button variante="fantasma" tamanho="sm" icone="pencil" :href="route('admin.participantes.edit', $p)" title="Editar" />
                        <x-excluir :action="route('admin.participantes.destroy', $p)" :descricao="$p->nome_completo" />
                    @endcan
                </td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $participantes->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
