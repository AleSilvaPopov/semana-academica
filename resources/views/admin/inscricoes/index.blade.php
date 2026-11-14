<x-layouts.admin titulo="Inscrições e presença">
    <x-cabecalho titulo="Inscrições e presença" :subtitulo="'Escolha a atividade para ver os inscritos e fazer o credenciamento · '.$edicaoAtual?->titulo" />

    <x-filtros placeholder="Buscar atividade...">
        <x-filtro-select name="tipo" :opcoes="$tipos" todos="Todos os tipos" class="lg:w-36" />
        <x-filtro-select name="status" :opcoes="$status" todos="Todos os status" class="lg:w-36" />
    </x-filtros>

    <x-table :vazia="$atividades->isEmpty()" vazio="Nenhuma atividade nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="nome">Atividade</x-th>
            <x-th coluna="data">Data</x-th>
            <x-th>Status</x-th>
            <x-th coluna="inscritos">Inscritos/vagas</x-th>
            <x-th coluna="presentes" class="text-right">Presentes</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($atividades as $a)
            <tr>
                <td>
                    <a href="{{ route('admin.inscricoes.show', $a) }}" class="link">{{ $a->nome }}</a>
                    <p class="text-xs text-zinc-500">{{ $a->coordenadoria?->nome }} · {{ $a->local?->nome_curto }}</p>
                </td>
                <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                <td><x-badge :enum="$a->status" /></td>
                <td class="min-w-36">
                    <span class="text-sm font-medium tabular-nums">{{ $a->inscricoes_count }}/{{ $a->vagas }}</span>
                    <x-barra-progresso :valor="$a->inscricoes_count" :total="$a->vagas" class="mt-1" />
                </td>
                <td class="text-right tabular-nums">{{ $a->presentes_count }}</td>
                <td class="text-right"><x-button variante="secundario" tamanho="sm" icone="ticket" :href="route('admin.inscricoes.show', $a)">Gerenciar</x-button></td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $atividades->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
