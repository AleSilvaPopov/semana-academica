<x-layouts.admin :titulo="$ministrante->participante->nome_completo">
    <x-cabecalho :titulo="$ministrante->participante->nome_completo" subtitulo="Ministrante" :voltar="route('admin.ministrantes.index')">
        <x-button variante="secundario" icone="user" :href="route('admin.participantes.show', $ministrante->cpf)">Cadastro de participante</x-button>
        @can('cadastros-globais')
            <x-button variante="secundario" icone="pencil" :href="route('admin.ministrantes.edit', $ministrante)">Editar</x-button>
            <x-excluir :action="route('admin.ministrantes.destroy', $ministrante)" :descricao="'o ministrante '.$ministrante->participante->nome_completo" rotulo="Remover" tamanho="md" texto="Remover" />
        @endcan
    </x-cabecalho>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-secao>
                <div class="flex flex-col items-center text-center">
                    <x-avatar :nome="$ministrante->participante->nome_completo" :url="$ministrante->imagem_url" tamanho="size-28" class="text-2xl" />
                    <p class="mt-3 font-semibold text-zinc-900">{{ $ministrante->participante->nome_completo }}</p>
                    <p class="text-sm text-zinc-500">{{ $ministrante->participante->instituicao_origem }}</p>
                    <div class="mt-2"><x-badge :enum="$ministrante->participante->tipo_participante" /></div>
                    @if ($ministrante->imagem_divulgacao && ! $ministrante->imagem_url)
                        <p class="mt-3 text-xs text-amber-700">Arquivo <code>{{ $ministrante->imagem_divulgacao }}</code> não encontrado; exibindo as iniciais.</p>
                    @endif
                </div>
            </x-secao>
            <x-secao titulo="Certificados">
                <ul class="space-y-1.5 text-sm">
                    @forelse ($ministrante->certificados as $c)
                        <li class="flex gap-2"><x-icon name="academic" class="size-4 shrink-0 text-brand-500" /> {{ $c->certificado }}</li>
                    @empty
                        <li class="text-zinc-500">Nenhum certificado cadastrado.</li>
                    @endforelse
                </ul>
            </x-secao>
        </div>
        <x-table class="lg:col-span-2" :vazia="$ministrante->atividades->isEmpty()" vazio="Ainda não ministrou atividades.">
            <x-slot:cabecalho><x-th>Atividade</x-th><x-th>Edição</x-th><x-th>Data</x-th><x-th>Local</x-th><x-th>Status</x-th></x-slot:cabecalho>
            @foreach ($ministrante->atividades as $a)
                <tr>
                    <td><a href="{{ route('admin.atividades.show', $a) }}" class="link">{{ $a->nome }}</a><br><x-badge :enum="$a->tipo" /></td>
                    <td class="whitespace-nowrap">{{ $a->coordenadoria?->comissao?->edicao }}ª</td>
                    <td class="whitespace-nowrap">{{ formata_data_hora($a->data_realizacao) }}</td>
                    <td class="whitespace-nowrap">{{ $a->local?->nome_curto }}</td>
                    <td><x-badge :enum="$a->status" /></td>
                </tr>
            @endforeach
        </x-table>
    </div>
</x-layouts.admin>
