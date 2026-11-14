<x-layouts.admin :titulo="'Tarefa #'.$tarefa->id_tarefa">
    <x-cabecalho :titulo="'Tarefa #'.$tarefa->id_tarefa" :subtitulo="$coordenadoria ? $coordenadoria->nome.' · '.$coordenadoria->comissao->titulo : null" :voltar="route('admin.tarefas.index')">
        @can('update', $tarefa)
            <x-button variante="secundario" icone="pencil" :href="route('admin.tarefas.edit', $tarefa)">Editar</x-button>
            <x-excluir :action="route('admin.tarefas.destroy', $tarefa)" :descricao="'a tarefa #'.$tarefa->id_tarefa" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    @if ($tarefa->atrasada)
        <x-alert tipo="erro" class="mb-6">Tarefa atrasada: o prazo terminou em {{ formata_data($tarefa->prazo_final) }}.</x-alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <x-secao titulo="Descrição" class="lg:col-span-2">
            <p class="text-sm whitespace-pre-line text-zinc-800">{{ $tarefa->descricao }}</p>
            <dl class="mt-4 divide-y divide-zinc-100 border-t border-zinc-100 pt-2">
                <x-detalhe rotulo="Criada por">{{ $tarefa->criador?->nome_completo }} <span class="text-xs text-zinc-500">({{ $tarefa->rga_criador }})</span></x-detalhe>
                <x-detalhe rotulo="Criada em">{{ formata_data($tarefa->data_criacao) }}</x-detalhe>
                <x-detalhe rotulo="Prazo final">{{ formata_data($tarefa->prazo_final) }}</x-detalhe>
                <x-detalhe rotulo="Status">
                    @can('alterarStatus', $tarefa)
                        @include('admin.tarefas._status')
                    @else
                        <x-badge :enum="$tarefa->status" />
                    @endcan
                </x-detalhe>
            </dl>
        </x-secao>
        <x-secao :titulo="'Responsáveis ('.$tarefa->responsaveis->count().')'">
            @forelse ($tarefa->responsaveis as $r)
                <div class="border-b border-zinc-100 py-2 last:border-0">
                    <a href="{{ route('admin.integrantes.show', $r) }}" class="link text-sm">{{ $r->nome_completo }}</a>
                    <p class="text-xs text-zinc-500">{{ $r->funcao }}{{ $r->coordenadoria ? ' · '.$r->coordenadoria->nome : '' }}</p>
                </div>
            @empty
                <p class="text-sm text-zinc-500">Nenhum responsável atribuído.</p>
            @endforelse
        </x-secao>
    </div>
</x-layouts.admin>
