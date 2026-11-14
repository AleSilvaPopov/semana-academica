<x-layouts.admin titulo="Certificados">
    <x-cabecalho titulo="Certificados" :subtitulo="'Certificados de participação (presença confirmada) · '.$edicaoAtual?->titulo" />

    <x-filtros placeholder="Buscar atividade...">
        <x-filtro-select name="situacao" :opcoes="['com' => 'Com presenças registradas']" todos="Todas as atividades" class="lg:w-60" />
    </x-filtros>

    <x-table :vazia="$atividades->isEmpty()" vazio="Nenhuma atividade nesta edição.">
        <x-slot:cabecalho>
            <x-th coluna="nome">Atividade</x-th>
            <x-th coluna="data">Data</x-th>
            <x-th class="text-right">Inscritos</x-th>
            <x-th coluna="presentes" class="text-right">Certificados</x-th>
            <x-th class="text-right">Ações</x-th>
        </x-slot:cabecalho>
        @foreach ($atividades as $a)
            <tr>
                <td><a href="{{ route('admin.certificados.show', $a) }}" class="link">{{ $a->nome }}</a> <x-badge :enum="$a->tipo" class="ml-1" /></td>
                <td class="whitespace-nowrap">{{ formata_data($a->data_realizacao) }}</td>
                <td class="text-right tabular-nums">{{ $a->inscricoes_count }}</td>
                <td class="text-right tabular-nums">
                    @if ($a->presentes_count)<x-badge cor="green">{{ $a->presentes_count }}</x-badge>@else<span class="text-zinc-400">0</span>@endif
                </td>
                <td class="text-right"><x-button variante="secundario" tamanho="sm" icone="document" :href="route('admin.certificados.show', $a)">Ver presentes</x-button></td>
            </tr>
        @endforeach
        <x-slot:paginacao>{{ $atividades->links() }}</x-slot:paginacao>
    </x-table>
</x-layouts.admin>
