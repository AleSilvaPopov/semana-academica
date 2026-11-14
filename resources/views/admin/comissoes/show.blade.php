<x-layouts.admin :titulo="$comissao->titulo">
    <x-cabecalho :titulo="$comissao->nome_completo" :subtitulo="$comissao->periodo.' · '.$comissao->local_sede" :voltar="route('admin.comissoes.index')">
        @if ($edicaoAtual?->id_comissao !== $comissao->id_comissao)
            <form method="POST" action="{{ route('admin.edicao.selecionar') }}">
                @csrf
                <input type="hidden" name="id_comissao" value="{{ $comissao->id_comissao }}">
                <x-button type="submit" variante="secundario" icone="check">Selecionar esta edição</x-button>
            </form>
        @endif
        @can('update', $comissao)
            <x-button variante="secundario" icone="pencil" :href="route('admin.comissoes.edit', $comissao)">Editar</x-button>
            <x-excluir :action="route('admin.comissoes.destroy', $comissao)" :descricao="'a '.$comissao->titulo" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-estatistica titulo="Presidente" :valor="$comissao->presidencia?->presidente?->nome_completo ?? '—'" icone="user" class="[&_p.text-2xl]:text-base" />
        <x-estatistica titulo="Vice-presidente" :valor="$comissao->presidencia?->vice?->nome_completo ?? '—'" icone="user" class="[&_p.text-2xl]:text-base" />
        <x-estatistica titulo="Atividades" :valor="$comissao->atividades->count()" icone="presentation" :detalhe="$comissao->atividades->sum('inscricoes_count').' inscrições'" />
        <x-estatistica titulo="Patrocínio financeiro" :valor="formata_moeda($comissao->patrocinios->sum('valor'), 'R$ 0,00')" icone="money" cor="green" :detalhe="$comissao->patrocinios->count().' patrocinador(es)'" />
    </div>

    @if ($comissao->observacoes)
        <x-alert tipo="info" class="mb-6">{{ $comissao->observacoes }}</x-alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <x-table :vazia="$comissao->coordenadorias->isEmpty()" vazio="Nenhuma coordenadoria cadastrada.">
            <x-slot:cabecalho><x-th>Coordenadoria</x-th><x-th>Coordenador(a)</x-th><x-th class="text-right">Membros</x-th><x-th class="text-right">Ativ.</x-th></x-slot:cabecalho>
            @foreach ($comissao->coordenadorias as $co)
                <tr>
                    <td><a href="{{ route('admin.coordenadorias.show', $co) }}" class="link">{{ $co->nome }}</a></td>
                    <td class="text-zinc-600">{{ $co->coordenador?->nome_completo }}</td>
                    <td class="text-right tabular-nums">{{ $co->quantidade?->quantidade_integrantes ?? 0 }}</td>
                    <td class="text-right tabular-nums">{{ $co->atividades_count }}</td>
                </tr>
            @endforeach
        </x-table>

        <x-table :vazia="$comissao->patrocinios->isEmpty()" vazio="Nenhum patrocinador nesta edição.">
            <x-slot:cabecalho><x-th>Patrocinador</x-th><x-th>Tipo</x-th><x-th class="text-right">Valor</x-th></x-slot:cabecalho>
            @foreach ($comissao->patrocinios as $pt)
                <tr>
                    <td><a href="{{ route('admin.patrocinadores.show', $pt->nome_patrocinador) }}" class="link">{{ $pt->nome_patrocinador }}</a>
                        <p class="text-xs text-zinc-500">{{ $pt->descricao_patrocinio }}</p></td>
                    <td><x-badge :enum="$pt->tipo_patrocinio" /></td>
                    <td class="text-right tabular-nums">{{ formata_moeda($pt->valor) }}</td>
                </tr>
            @endforeach
        </x-table>

        <x-table class="lg:col-span-2" :vazia="$comissao->atividades->isEmpty()" vazio="Nenhuma atividade cadastrada.">
            <x-slot:cabecalho><x-th>Atividade</x-th><x-th>Data</x-th><x-th>Local</x-th><x-th>Coordenadoria</x-th><x-th>Status</x-th><x-th class="text-right">Inscritos/vagas</x-th></x-slot:cabecalho>
            @foreach ($comissao->atividades as $a)
                <tr>
                    <td><a href="{{ route('admin.atividades.show', $a) }}" class="link">{{ $a->nome }}</a> <x-badge :enum="$a->tipo" class="ml-1" /></td>
                    <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                    <td class="whitespace-nowrap">{{ $a->local?->nome_curto }}</td>
                    <td>{{ $a->coordenadoria?->nome }}</td>
                    <td><x-badge :enum="$a->status" /></td>
                    <td class="text-right tabular-nums">{{ $a->inscricoes_count }}/{{ $a->vagas }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
</x-layouts.admin>
