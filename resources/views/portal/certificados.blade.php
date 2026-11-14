<x-layouts.portal titulo="Meus certificados">
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Meus certificados</h1>
        <p class="mt-1 text-sm text-zinc-500">Certificados das atividades em que sua presença foi confirmada.</p>

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            @forelse ($inscricoes as $i)
                <div class="cartao flex flex-col p-5">
                    <div class="flex items-start gap-3">
                        <div class="rounded-lg bg-brand-50 p-2 text-brand-600"><x-icon name="document" class="size-6" /></div>
                        <div class="min-w-0">
                            <p class="font-semibold text-zinc-900">{{ $i->atividade->nome }}</p>
                            <p class="text-sm text-zinc-500">{{ $i->atividade->tipo->rotulo() }} · {{ carga_horaria($i->atividade->duracao_minutos) }}</p>
                            <p class="text-xs text-zinc-500">{{ formata_data($i->atividade->data_realizacao) }} · {{ $i->atividade->coordenadoria?->comissao?->titulo }}</p>
                        </div>
                    </div>
                    <x-button class="mt-4" variante="secundario" icone="printer" :href="route('portal.certificados.show', $i->atividade)" target="_blank">Abrir e imprimir</x-button>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center sm:col-span-2">
                    <x-icon name="document" class="mx-auto size-8 text-zinc-400" />
                    <p class="mt-3 text-sm text-zinc-500">Você ainda não tem certificados. Eles aparecem aqui depois que a organização confirma sua presença.</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.portal>
