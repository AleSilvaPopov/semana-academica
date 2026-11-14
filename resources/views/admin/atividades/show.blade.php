<x-layouts.admin :titulo="$atividade->nome">
    <x-cabecalho :titulo="$atividade->nome" :subtitulo="$atividade->coordenadoria->comissao->titulo.' · '.$atividade->coordenadoria->nome" :voltar="route('admin.atividades.index')">
        <x-button variante="secundario" icone="ticket" :href="route('admin.inscricoes.show', $atividade)">Inscrições e presença</x-button>
        <x-button variante="secundario" icone="eye" :href="route('portal.atividades.show', $atividade)" target="_blank">Ver no portal</x-button>
        @can('update', $atividade)
            <x-button variante="secundario" icone="pencil" :href="route('admin.atividades.edit', $atividade)">Editar</x-button>
            <x-excluir :action="route('admin.atividades.destroy', $atividade)" :descricao="'a atividade '.$atividade->nome" tamanho="md" texto="Excluir" />
        @endcan
    </x-cabecalho>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-estatistica titulo="Inscritos / vagas" :valor="$atividade->inscricoes_count.' / '.$atividade->vagas" icone="users" :detalhe="max(0, $atividade->vagas - $atividade->inscricoes_count).' vaga(s) restante(s)'" />
        <x-estatistica titulo="Presenças" :valor="$atividade->presentes_count" icone="check" cor="green"
            :detalhe="$atividade->inscricoes_count ? round($atividade->presentes_count / $atividade->inscricoes_count * 100).'% dos inscritos' : 'sem inscritos'" />
        <x-estatistica titulo="Data" :valor="formata_data($atividade->data_realizacao)" icone="calendar" :detalhe="$atividade->data_realizacao->format('H:i').' · '.formata_duracao($atividade->duracao_minutos)" />
        <x-estatistica titulo="Inscrição" :valor="(float) $atividade->valor_inscricao > 0 ? formata_moeda($atividade->valor_inscricao) : 'Gratuita'" icone="money" cor="zinc" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-secao titulo="Detalhes" class="lg:col-span-2">
            <p class="mb-4 text-sm whitespace-pre-line text-zinc-700">{{ $atividade->descricao }}</p>
            <dl class="divide-y divide-zinc-100">
                <x-detalhe rotulo="Tipo"><x-badge :enum="$atividade->tipo" /></x-detalhe>
                <x-detalhe rotulo="Status"><x-badge :enum="$atividade->status" /></x-detalhe>
                <x-detalhe rotulo="Local">
                    {{ $atividade->local->rotulo }}
                    <span class="block text-xs text-zinc-500">{{ $atividade->local->faculdade }}</span>
                    @if ($atividade->local->equipamentos->isNotEmpty())
                        <span class="mt-1 flex flex-wrap gap-1">@foreach ($atividade->local->equipamentos as $e)<x-badge>{{ $e->equipamento }}</x-badge>@endforeach</span>
                    @endif
                </x-detalhe>
                <x-detalhe rotulo="Coordenadoria">
                    <a href="{{ route('admin.coordenadorias.show', $atividade->coordenadoria) }}" class="link">{{ $atividade->coordenadoria->nome }}</a>
                    <span class="block text-xs text-zinc-500">Coordenação: {{ $atividade->coordenadoria->coordenador?->nome_completo }}</span>
                </x-detalhe>
            </dl>
        </x-secao>
        <x-secao :titulo="'Ministrantes ('.$atividade->ministrantes->count().')'">
            @forelse ($atividade->ministrantes as $m)
                <a href="{{ route('admin.ministrantes.show', $m) }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-zinc-50">
                    <x-avatar :nome="$m->participante->nome_completo" :url="$m->imagem_url" />
                    <div>
                        <p class="text-sm font-medium text-zinc-900">{{ $m->participante->nome_completo }}</p>
                        <p class="text-xs text-zinc-500">{{ $m->certificados->first()?->certificado ?? $m->participante->instituicao_origem }}</p>
                    </div>
                </a>
            @empty
                <p class="text-sm text-zinc-500">Nenhum ministrante vinculado.</p>
            @endforelse
        </x-secao>
    </div>
</x-layouts.admin>
