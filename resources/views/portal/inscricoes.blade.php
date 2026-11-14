<x-layouts.portal titulo="Minhas inscrições">
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Minhas inscrições</h1>
        <p class="mt-1 text-sm text-zinc-500">Você pode cancelar uma inscrição até a data da atividade.</p>

        <h2 class="mt-8 mb-3 text-sm font-semibold tracking-wide text-zinc-500 uppercase">Próximas ({{ $futuras->count() }})</h2>
        <div class="space-y-3">
            @forelse ($futuras as $i)
                <div class="cartao flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <a href="{{ route('portal.atividades.show', $i->atividade) }}" class="font-semibold text-zinc-900 hover:text-brand-700">{{ $i->atividade->nome }}</a>
                        <p class="text-sm text-zinc-500">{{ formata_data_hora($i->atividade->data_realizacao) }} · {{ $i->atividade->local?->nome_curto }}</p>
                        <div class="mt-1 flex gap-1.5"><x-badge :enum="$i->atividade->tipo" /><x-badge :enum="$i->atividade->status" /></div>
                    </div>
                    <form method="POST" action="{{ route('portal.inscricoes.destroy', $i->atividade) }}" onsubmit="return confirm('Cancelar sua inscrição em {{ addslashes($i->atividade->nome) }}?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variante="secundario" tamanho="sm" class="w-full sm:w-auto">Cancelar inscrição</x-button>
                    </form>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-zinc-300 p-8 text-center">
                    <p class="text-sm text-zinc-500">Você não tem inscrições em atividades futuras.</p>
                    <x-button class="mt-3" :href="route('portal.programacao')">Ver programação</x-button>
                </div>
            @endforelse
        </div>

        @if ($passadas->isNotEmpty())
            <h2 class="mt-10 mb-3 text-sm font-semibold tracking-wide text-zinc-500 uppercase">Histórico ({{ $passadas->count() }})</h2>
            <div class="cartao divide-y divide-zinc-100">
                @foreach ($passadas as $i)
                    <div class="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium text-zinc-800">{{ $i->atividade->nome }}</p>
                            <p class="text-xs text-zinc-500">{{ formata_data($i->atividade->data_realizacao) }} · {{ $i->atividade->coordenadoria?->comissao?->titulo }}</p>
                        </div>
                        @if ($i->presenca)
                            <x-button variante="secundario" tamanho="sm" icone="document" :href="route('portal.certificados.show', $i->atividade)">Certificado</x-button>
                        @else
                            <x-badge>Sem presença</x-badge>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.portal>
