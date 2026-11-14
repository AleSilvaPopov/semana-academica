<x-layouts.admin :titulo="$coordenadoria->nome">
    <x-cabecalho :titulo="$coordenadoria->nome" :subtitulo="$coordenadoria->comissao->titulo" :voltar="route('admin.coordenadorias.index')">
        @can('update', $coordenadoria)
            <x-button variante="secundario" icone="pencil" :href="route('admin.coordenadorias.edit', $coordenadoria)">Editar</x-button>
            <x-excluir :action="route('admin.coordenadorias.destroy', $coordenadoria)" :descricao="'a coordenadoria '.$coordenadoria->nome" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-secao titulo="Dados">
                <p class="mb-3 text-sm text-zinc-700">{{ $coordenadoria->descricao_atuacao }}</p>
                <dl class="divide-y divide-zinc-100">
                    <x-detalhe rotulo="Coordenador(a)">
                        @if ($coordenadoria->coordenador)
                            <a href="{{ route('admin.integrantes.show', $coordenadoria->coordenador) }}" class="link">{{ $coordenadoria->coordenador->nome_completo }}</a>
                        @endif
                    </x-detalhe>
                    <x-detalhe rotulo="Período">{{ formata_data($coordenadoria->data_inicio_coordenacao) }} a {{ formata_data($coordenadoria->data_termino_coordenacao, 'atual') }}</x-detalhe>
                    <x-detalhe rotulo="Integrantes (view)">{{ $coordenadoria->quantidade?->quantidade_integrantes ?? 0 }}</x-detalhe>
                </dl>
            </x-secao>
            <x-secao :titulo="'Membros ('.$coordenadoria->membros->count().')'" descricao="Integrantes com id_coordenadoria apontando para cá">
                @forelse ($coordenadoria->membros as $m)
                    <div class="flex items-center justify-between border-b border-zinc-100 py-2 last:border-0">
                        <div>
                            <a href="{{ route('admin.integrantes.show', $m) }}" class="link text-sm">{{ $m->nome_completo }}</a>
                            <p class="text-xs text-zinc-500">{{ $m->funcao }}</p>
                        </div>
                        @if ($m->rga === $coordenadoria->rga_coordenador)<x-badge cor="purple">Coordenador</x-badge>@endif
                    </div>
                @empty
                    <p class="text-sm text-zinc-500">Nenhum integrante atua nesta coordenadoria.</p>
                @endforelse
            </x-secao>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-table :vazia="$coordenadoria->atividades->isEmpty()" vazio="Nenhuma atividade organizada por esta coordenadoria.">
                <x-slot:cabecalho><x-th>Atividade</x-th><x-th>Data</x-th><x-th>Local</x-th><x-th>Status</x-th><x-th class="text-right">Inscritos</x-th></x-slot:cabecalho>
                @foreach ($coordenadoria->atividades as $a)
                    <tr>
                        <td><a href="{{ route('admin.atividades.show', $a) }}" class="link">{{ $a->nome }}</a></td>
                        <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                        <td class="whitespace-nowrap">{{ $a->local?->nome_curto }}</td>
                        <td><x-badge :enum="$a->status" /></td>
                        <td class="text-right tabular-nums">{{ $a->inscricoes_count }}/{{ $a->vagas }}</td>
                    </tr>
                @endforeach
            </x-table>

            <x-table :vazia="$tarefas->isEmpty()" vazio="Nenhuma tarefa criada nesta coordenação.">
                <x-slot:cabecalho><x-th>Tarefa</x-th><x-th>Prazo</x-th><x-th>Responsáveis</x-th><x-th>Status</x-th></x-slot:cabecalho>
                @foreach ($tarefas as $t)
                    <tr @class(['bg-red-50/60' => $t->atrasada])>
                        <td><a href="{{ route('admin.tarefas.show', $t) }}" class="link">{{ \Illuminate\Support\Str::limit($t->descricao, 70) }}</a></td>
                        <td class="whitespace-nowrap">{{ formata_data($t->prazo_final) }}</td>
                        <td class="text-xs text-zinc-600">{{ $t->responsaveis->pluck('nome_completo')->join(', ') ?: '—' }}</td>
                        <td><x-badge :enum="$t->status" />@if ($t->atrasada)<x-badge cor="red" class="ml-1">Atrasada</x-badge>@endif</td>
                    </tr>
                @endforeach
            </x-table>
        </div>
    </div>
</x-layouts.admin>
