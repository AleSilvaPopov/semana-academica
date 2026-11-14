<x-layouts.admin :titulo="'Inscrições · '.$atividade->nome">
    @php
        $pode = auth('integrante')->user()->can('gerenciarInscricoes', $atividade);
        $podeMarcar = $pode && ! $atividade->data_realizacao->isAfter(today()->endOfDay());
    @endphp
    <x-cabecalho :titulo="$atividade->nome" :subtitulo="formata_data_hora($atividade->data_realizacao).' · '.$atividade->local->nome_curto.' · '.$atividade->coordenadoria->nome" :voltar="route('admin.inscricoes.index')">
        <x-button variante="secundario" icone="eye" :href="route('admin.atividades.show', $atividade)">Atividade</x-button>
        <x-button variante="secundario" icone="document" :href="route('admin.certificados.show', $atividade)">Certificados</x-button>
    </x-cabecalho>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-estatistica titulo="Inscritos / vagas" :valor="$atividade->inscricoes_count.' / '.$atividade->vagas" icone="users" :detalhe="max(0, $atividade->vagas - $atividade->inscricoes_count).' vaga(s) restante(s)'" />
        <x-estatistica titulo="Presentes" :valor="$presentes" icone="check" cor="green" :detalhe="$atividade->inscricoes_count ? round($presentes / $atividade->inscricoes_count * 100).'% de presença' : '—'" />
        <x-estatistica titulo="Status" :valor="$atividade->status->rotulo()" icone="flag" cor="zinc" :detalhe="$atividade->inscricoes_abertas ? 'Inscrições abertas' : 'Inscrições encerradas'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2" x-data="presencaLote">
            <form id="form-presenca" method="POST" action="{{ route('admin.inscricoes.presenca', $atividade) }}">
                @csrf
                @method('PATCH')
            </form>

            <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" class="flex gap-2">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar inscrito por nome ou CPF..." class="campo sm:w-72">
                    <select name="presenca" class="campo w-auto pr-8" onchange="this.form.submit()" aria-label="Filtrar presença">
                        <option value="">Todos</option>
                        <option value="1" @selected(request('presenca') === '1')>Presentes</option>
                        <option value="0" @selected(request('presenca') === '0')>Ausentes</option>
                    </select>
                    <x-button type="submit" variante="secundario" icone="search" />
                </form>
                @if ($podeMarcar && $inscritos->isNotEmpty())
                    <div class="flex gap-2">
                        <x-button variante="fantasma" tamanho="sm" x-on:click="marcarTodos(true)">Marcar todos</x-button>
                        <x-button variante="fantasma" tamanho="sm" x-on:click="marcarTodos(false)">Desmarcar</x-button>
                    </div>
                @endif
            </div>

            @if ($pode && ! $podeMarcar)
                <x-alert tipo="info" class="mb-3">A presença poderá ser registrada a partir do dia da atividade ({{ formata_data($atividade->data_realizacao) }}).</x-alert>
            @endif

            <x-table :vazia="$inscritos->isEmpty()" vazio="Nenhum inscrito encontrado.">
                <x-slot:cabecalho>
                    <x-th class="w-12">Presente</x-th>
                    <x-th>Participante</x-th>
                    <x-th>Tipo</x-th>
                    <x-th>Inscrição</x-th>
                    @if ($pode)<x-th class="text-right">Ações</x-th>@endif
                </x-slot:cabecalho>
                @foreach ($inscritos as $p)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" form="form-presenca" name="presentes[]" value="{{ $p->cpf }}" data-presenca
                                @checked($p->pivot->presenca) @disabled(! $podeMarcar)
                                class="size-5 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" aria-label="Presença de {{ $p->nome_completo }}">
                        </td>
                        <td>
                            <a href="{{ route('admin.participantes.show', $p) }}" class="font-medium text-zinc-900 hover:underline">{{ $p->nome_completo }}</a>
                            <p class="text-xs text-zinc-500">{{ formata_cpf($p->cpf) }} · {{ $p->instituicao_origem }}</p>
                        </td>
                        <td><x-badge :enum="$p->tipo_participante" /></td>
                        <td class="text-xs whitespace-nowrap">{{ formata_data_hora($p->pivot->data_inscricao) }}</td>
                        @if ($pode)
                            <td class="text-right">
                                @unless ($p->pivot->presenca)
                                    <x-excluir :action="route('admin.inscricoes.destroy', ['atividade' => $atividade, 'participante' => $p->cpf])"
                                        :descricao="'a inscrição de '.$p->nome_completo" rotulo="Remover" />
                                @endunless
                            </td>
                        @endif
                    </tr>
                @endforeach
            </x-table>

            @if ($podeMarcar && $inscritos->isNotEmpty())
                <div class="mt-4 flex items-center justify-between gap-3">
                    <p class="text-xs text-zinc-500">Os inscritos marcados ficam como presentes e os desmarcados como ausentes.</p>
                    <x-button type="submit" form="form-presenca" variante="sucesso" icone="check">Salvar presenças</x-button>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($pode)
                <x-secao titulo="Inscrição manual" descricao="Aplica as mesmas regras do portal">
                    @if ($atividade->inscricoes_abertas)
                        <form method="POST" action="{{ route('admin.inscricoes.store', $atividade) }}" class="space-y-3">
                            @csrf
                            <x-select name="cpf" label="Participante" :opcoes="$disponiveis" vazio="Selecione..." />
                            <x-button type="submit" icone="plus" class="w-full" :disabled="$atividade->inscricoes_count >= $atividade->vagas">
                                {{ $atividade->inscricoes_count >= $atividade->vagas ? 'Atividade lotada' : 'Inscrever' }}
                            </x-button>
                        </form>
                    @else
                        <p class="text-sm text-zinc-500">Inscrições encerradas: a atividade já aconteceu ou está {{ mb_strtolower($atividade->status->rotulo()) }}.</p>
                    @endif
                </x-secao>
            @else
                <x-alert tipo="info">Você está em modo leitura. Só a presidência da edição e a coordenação de "{{ $atividade->coordenadoria->nome }}" gerenciam estas inscrições.</x-alert>
            @endif
        </div>
    </div>
</x-layouts.admin>
