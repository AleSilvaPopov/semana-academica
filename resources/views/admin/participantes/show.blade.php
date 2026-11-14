<x-layouts.admin :titulo="$participante->nome_completo">
    <x-cabecalho :titulo="$participante->nome_completo" :subtitulo="$participante->email" :voltar="route('admin.participantes.index')">
        @can('cadastros-globais')
            @if (! $participante->ministrante)
                <x-button variante="secundario" icone="microphone" :href="route('admin.ministrantes.create', ['cpf' => $participante->cpf])">Tornar ministrante</x-button>
            @endif
            <x-button variante="secundario" icone="pencil" :href="route('admin.participantes.edit', $participante)">Editar</x-button>
            <x-excluir :action="route('admin.participantes.destroy', $participante)" :descricao="$participante->nome_completo" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-secao titulo="Dados pessoais">
                <dl class="divide-y divide-zinc-100">
                    <x-detalhe rotulo="CPF">{{ formata_cpf($participante->cpf) }}</x-detalhe>
                    <x-detalhe rotulo="Nascimento">{{ formata_data($participante->data_nascimento) }}</x-detalhe>
                    <x-detalhe rotulo="País">{{ $participante->pais }}</x-detalhe>
                    <x-detalhe rotulo="Tipo"><x-badge :enum="$participante->tipo_participante" /></x-detalhe>
                    <x-detalhe rotulo="Instituição">{{ $participante->instituicao_origem }}</x-detalhe>
                    <x-detalhe rotulo="Conta"><x-badge :enum="$participante->status_conta" /></x-detalhe>
                    <x-detalhe rotulo="Telefones">
                        @forelse ($participante->telefones as $t)
                            <span class="block">{{ formata_telefone($t->telefone) }}</span>
                        @empty
                            —
                        @endforelse
                    </x-detalhe>
                </dl>
            </x-secao>
            @if ($participante->ministrante)
                <x-secao titulo="Ministrante">
                    <p class="text-sm text-zinc-600">{{ $participante->ministrante->certificados->count() }} certificado(s) cadastrado(s).</p>
                    <x-button variante="secundario" tamanho="sm" class="mt-3" :href="route('admin.ministrantes.show', $participante->cpf)">Ver perfil de ministrante</x-button>
                </x-secao>
            @endif
        </div>

        <x-table class="lg:col-span-2" :vazia="$participante->inscricoes->isEmpty()" vazio="Nenhuma inscrição.">
            <x-slot:cabecalho><x-th>Atividade</x-th><x-th>Edição</x-th><x-th>Inscrição</x-th><x-th>Presença</x-th></x-slot:cabecalho>
            @foreach ($participante->inscricoes as $i)
                <tr>
                    <td><a class="link" href="{{ route('admin.atividades.show', $i->id_atividade) }}">{{ $i->atividade->nome }}</a>
                        <p class="text-xs text-zinc-500">{{ formata_data_hora($i->atividade->data_realizacao) }}</p></td>
                    <td class="whitespace-nowrap">{{ $i->atividade->coordenadoria?->comissao?->edicao }}ª</td>
                    <td class="whitespace-nowrap">{{ formata_data_hora($i->data_inscricao) }}</td>
                    <td>
                        @if ($i->presenca)
                            <x-badge cor="green">Presente</x-badge>
                        @else
                            <x-badge>Sem presença</x-badge>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-table>
    </div>
</x-layouts.admin>
