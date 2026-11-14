<x-layouts.admin :titulo="$local->nome_curto">
    <x-cabecalho :titulo="$local->nome_curto" :subtitulo="$local->faculdade" :voltar="route('admin.locais.index')">
        @can('cadastros-globais')
            <x-button variante="secundario" icone="pencil" :href="route('admin.locais.edit', $local)">Editar</x-button>
            <x-excluir :action="route('admin.locais.destroy', $local)" :descricao="'o local '.$local->nome_curto" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-secao titulo="Dados">
                <dl class="divide-y divide-zinc-100">
                    <x-detalhe rotulo="Tipo"><x-badge :enum="$local->tipo" /></x-detalhe>
                    <x-detalhe rotulo="Bloco">{{ $local->bloco }}</x-detalhe>
                    <x-detalhe rotulo="Número">{{ $local->numero }}</x-detalhe>
                    <x-detalhe rotulo="Capacidade">{{ $local->capacidade }} lugares</x-detalhe>
                    <x-detalhe rotulo="Observações">{{ $local->observacoes ?? '—' }}</x-detalhe>
                </dl>
            </x-secao>
            <x-secao titulo="Equipamentos">
                <div class="flex flex-wrap gap-1.5">
                    @forelse ($local->equipamentos as $e)
                        <x-badge>{{ $e->equipamento }}</x-badge>
                    @empty
                        <p class="text-sm text-zinc-500">Nenhum equipamento cadastrado.</p>
                    @endforelse
                </div>
            </x-secao>
        </div>
        <x-table class="lg:col-span-2" :vazia="$local->atividades->isEmpty()" vazio="Nenhuma atividade neste local.">
            <x-slot:cabecalho><x-th>Atividade</x-th><x-th>Edição</x-th><x-th>Data</x-th><x-th class="text-right">Inscritos/vagas</x-th></x-slot:cabecalho>
            @foreach ($local->atividades as $a)
                <tr>
                    <td><a href="{{ route('admin.atividades.show', $a) }}" class="link">{{ $a->nome }}</a></td>
                    <td class="whitespace-nowrap">{{ $a->coordenadoria?->comissao?->edicao }}ª</td>
                    <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                    <td class="text-right tabular-nums">{{ $a->inscricoes_count }}/{{ $a->vagas }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
</x-layouts.admin>
