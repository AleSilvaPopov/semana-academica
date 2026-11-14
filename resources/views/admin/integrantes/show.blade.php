<x-layouts.admin :titulo="$integrante->nome_completo">
    <x-cabecalho :titulo="$integrante->nome_completo" :subtitulo="'RGA '.$integrante->rga.' · '.$integrante->funcao" :voltar="route('admin.integrantes.index')">
        @can('cadastros-globais')
            <x-button variante="secundario" icone="pencil" :href="route('admin.integrantes.edit', $integrante)">Editar</x-button>
            <x-excluir :action="route('admin.integrantes.destroy', $integrante)" :descricao="$integrante->nome_completo" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-secao titulo="Dados">
                <dl class="divide-y divide-zinc-100">
                    <x-detalhe rotulo="CPF">{{ formata_cpf($integrante->cpf) }}</x-detalhe>
                    <x-detalhe rotulo="Curso">{{ $integrante->curso?->nome_curso }}<span class="block text-xs text-zinc-500">{{ $integrante->curso?->faculdade }}</span></x-detalhe>
                    <x-detalhe rotulo="Ingresso">{{ formata_data($integrante->data_ingresso) }}</x-detalhe>
                    <x-detalhe rotulo="Atua em">
                        @if ($integrante->coordenadoria)
                            <a href="{{ route('admin.coordenadorias.show', $integrante->coordenadoria) }}" class="link">{{ $integrante->coordenadoria->nome }}</a>
                            <span class="block text-xs text-zinc-500">{{ $integrante->coordenadoria->comissao?->titulo }}</span>
                        @else
                            —
                        @endif
                    </x-detalhe>
                    <x-detalhe rotulo="Telefones">
                        @forelse ($integrante->telefones as $t)<span class="block">{{ formata_telefone($t->telefone) }}</span>@empty — @endforelse
                    </x-detalhe>
                    <x-detalhe rotulo="E-mails">
                        @forelse ($integrante->emails as $e)<span class="block break-all">{{ $e->email }}</span>@empty — @endforelse
                    </x-detalhe>
                    <x-detalhe rotulo="Observações">{{ $integrante->observacoes ?? '—' }}</x-detalhe>
                </dl>
            </x-secao>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-secao titulo="Histórico de presidências">
                @forelse ($presidencias as $p)
                    <div class="flex items-center justify-between border-b border-zinc-100 py-2 last:border-0">
                        <a href="{{ route('admin.comissoes.show', $p['comissao']) }}" class="link">{{ $p['comissao']->titulo }}</a>
                        <x-badge :cor="$p['papel'] === 'Presidente' ? 'purple' : 'sky'">{{ $p['papel'] }}</x-badge>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500">Nunca presidiu uma edição.</p>
                @endforelse
            </x-secao>

            <x-table :vazia="$integrante->coordenadoriasQueCoordena->isEmpty()" vazio="Nunca coordenou uma coordenadoria.">
                <x-slot:cabecalho><x-th>Coordenação</x-th><x-th>Edição</x-th><x-th>Período</x-th></x-slot:cabecalho>
                @foreach ($integrante->coordenadoriasQueCoordena as $c)
                    <tr>
                        <td><a href="{{ route('admin.coordenadorias.show', $c) }}" class="link">{{ $c->nome }}</a></td>
                        <td class="whitespace-nowrap">{{ $c->comissao?->titulo }}</td>
                        <td class="whitespace-nowrap">{{ formata_data($c->data_inicio_coordenacao) }} a {{ formata_data($c->data_termino_coordenacao, 'atual') }}</td>
                    </tr>
                @endforeach
            </x-table>

            <x-secao :titulo="'Tarefas como responsável ('.$integrante->tarefas->count().')'" :descricao="$integrante->tarefas_criadas_count.' tarefa(s) criada(s) como coordenador'">
                @forelse ($integrante->tarefas->take(8) as $t)
                    <div class="flex items-start justify-between gap-3 border-b border-zinc-100 py-2 last:border-0">
                        <a href="{{ route('admin.tarefas.show', $t) }}" class="link text-sm">{{ $t->descricao }}</a>
                        <div class="flex shrink-0 items-center gap-2 text-xs text-zinc-500">{{ formata_data($t->prazo_final) }} <x-badge :enum="$t->status" /></div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500">Nenhuma tarefa atribuída.</p>
                @endforelse
            </x-secao>
        </div>
    </div>
</x-layouts.admin>
