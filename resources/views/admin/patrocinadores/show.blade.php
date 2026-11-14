<x-layouts.admin :titulo="$patrocinador->nome">
    <x-cabecalho :titulo="$patrocinador->nome" subtitulo="Patrocinador" :voltar="route('admin.patrocinadores.index')">
        @can('cadastros-globais')
            <x-button variante="secundario" icone="pencil" :href="route('admin.patrocinadores.edit', $patrocinador)">Editar</x-button>
            <x-excluir :action="route('admin.patrocinadores.destroy', $patrocinador)" :descricao="$patrocinador->nome" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-secao titulo="Observações">
            <p class="text-sm whitespace-pre-line text-zinc-700">{{ $patrocinador->observacoes ?: 'Sem observações.' }}</p>
        </x-secao>
        <x-table class="lg:col-span-2" :vazia="$patrocinador->patrocinios->isEmpty()" vazio="Este patrocinador ainda não apoiou nenhuma edição.">
            <x-slot:cabecalho><x-th>Edição</x-th><x-th>Tipo</x-th><x-th>Descrição</x-th><x-th class="text-right">Valor</x-th></x-slot:cabecalho>
            @foreach ($patrocinador->patrocinios as $pt)
                <tr>
                    <td class="whitespace-nowrap"><a class="link" href="{{ route('admin.comissoes.show', $pt->id_comissao) }}">{{ $pt->comissao->titulo }}</a></td>
                    <td><x-badge :enum="$pt->tipo_patrocinio" /></td>
                    <td>{{ $pt->descricao_patrocinio }}</td>
                    <td class="text-right tabular-nums">{{ formata_moeda($pt->valor) }}</td>
                </tr>
            @endforeach
            @if ($patrocinador->patrocinios->isNotEmpty())
                <x-slot:rodape>
                    <tr><td colspan="3" class="px-4 py-3 text-right">Total financeiro</td><td class="px-4 py-3 text-right tabular-nums">{{ formata_moeda($patrocinador->patrocinios->sum('valor')) }}</td></tr>
                </x-slot:rodape>
            @endif
        </x-table>
    </div>
</x-layouts.admin>
