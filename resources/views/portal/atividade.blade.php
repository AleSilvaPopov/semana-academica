<x-layouts.portal :titulo="$atividade->nome">
    @php
        $a = $atividade;
        $restantes = $a->vagas_restantes;
        $edicao = $a->coordenadoria->comissao;
    @endphp
    <section class="border-b border-zinc-200 bg-zinc-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <a href="{{ route('portal.programacao', ['edicao' => $edicao->id_comissao]) }}" class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-800">
                <x-icon name="arrow-left" class="size-4" /> Programação da {{ $edicao->edicao }}ª edição
            </a>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <x-badge :enum="$a->tipo" />
                <x-badge :enum="$a->status" />
            </div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl">{{ $a->nome }}</h1>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            <div>
                <h2 class="mb-2 text-sm font-semibold tracking-wide text-zinc-500 uppercase">Sobre a atividade</h2>
                <p class="leading-relaxed whitespace-pre-line text-zinc-700">{{ $a->descricao }}</p>
            </div>

            @if ($a->ministrantes->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-semibold tracking-wide text-zinc-500 uppercase">{{ $a->ministrantes->count() > 1 ? 'Ministrantes' : 'Ministrante' }}</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($a->ministrantes as $m)
                            <div class="flex gap-4 rounded-xl border border-zinc-200 p-4">
                                <x-avatar :nome="$m->participante->nome_completo" :url="$m->imagem_url" tamanho="size-16" class="text-lg" />
                                <div class="min-w-0">
                                    <p class="font-semibold text-zinc-900">{{ $m->participante->nome_completo }}</p>
                                    <p class="text-sm text-zinc-500">{{ $m->participante->instituicao_origem }}</p>
                                    @if ($m->certificados->isNotEmpty())
                                        <ul class="mt-2 space-y-0.5 text-xs text-zinc-600">
                                            @foreach ($m->certificados->take(3) as $c)<li>• {{ $c->certificado }}</li>@endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <h2 class="mb-2 text-sm font-semibold tracking-wide text-zinc-500 uppercase">Local</h2>
                <p class="text-zinc-800">{{ $a->local->tipo->rotulo() }} {{ $a->local->numero }} — bloco {{ $a->local->bloco }}</p>
                <p class="text-sm text-zinc-500">{{ $a->local->faculdade }}</p>
                @if ($a->local->equipamentos->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1.5">@foreach ($a->local->equipamentos as $e)<x-badge>{{ $e->equipamento }}</x-badge>@endforeach</div>
                @endif
            </div>
        </div>

        {{-- Cartão de inscrição --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="cartao p-5">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center gap-3"><x-icon name="calendar" class="size-5 text-brand-600" />
                        <div><dt class="text-xs text-zinc-500">Data</dt><dd class="font-medium text-zinc-900 capitalize">{{ $a->data_realizacao->translatedFormat('l, d/m/Y \à\s H:i') }}</dd></div></div>
                    <div class="flex items-center gap-3"><x-icon name="clock" class="size-5 text-brand-600" />
                        <div><dt class="text-xs text-zinc-500">Duração</dt><dd class="font-medium text-zinc-900">{{ formata_duracao($a->duracao_minutos) }}</dd></div></div>
                    <div class="flex items-center gap-3"><x-icon name="money" class="size-5 text-brand-600" />
                        <div><dt class="text-xs text-zinc-500">Inscrição</dt><dd class="font-medium text-zinc-900">{{ (float) $a->valor_inscricao > 0 ? formata_moeda($a->valor_inscricao) : 'Gratuita' }}</dd></div></div>
                    <div class="flex items-center gap-3"><x-icon name="users" class="size-5 text-brand-600" />
                        <div class="flex-1"><dt class="text-xs text-zinc-500">Vagas</dt>
                            <dd class="font-medium text-zinc-900">{{ $restantes }} de {{ $a->vagas }} disponíveis</dd>
                            <x-barra-progresso :valor="$a->inscricoes_count" :total="$a->vagas" class="mt-1" /></div></div>
                </dl>

                <div class="mt-5 border-t border-zinc-100 pt-5">
                    @if ($inscricao)
                        <x-alert tipo="sucesso">Você está inscrito(a) desde {{ formata_data_hora($inscricao->data_inscricao) }}.</x-alert>
                        @if ($inscricao->presenca)
                            <x-button class="mt-3 w-full" icone="document" :href="route('portal.certificados.show', $a)">Ver certificado</x-button>
                        @elseif ($a->data_realizacao->isFuture())
                            <form method="POST" action="{{ route('portal.inscricoes.destroy', $a) }}" class="mt-3"
                                onsubmit="return confirm('Cancelar sua inscrição nesta atividade?')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variante="secundario" class="w-full">Cancelar inscrição</x-button>
                            </form>
                        @endif
                    @elseif (! $a->status->aceitaInscricao())
                        <x-alert tipo="aviso">Inscrições indisponíveis: atividade {{ mb_strtolower($a->status->rotulo()) }}.</x-alert>
                    @elseif (! $a->data_realizacao->isFuture())
                        <x-alert tipo="aviso">Inscrições encerradas: esta atividade já aconteceu.</x-alert>
                    @elseif ($restantes === 0)
                        <x-alert tipo="erro">Atividade esgotada: todas as vagas foram preenchidas.</x-alert>
                    @elseif (! auth('participante')->check())
                        <x-button class="w-full" tamanho="lg" :href="route('portal.login')">Entre para se inscrever</x-button>
                        <p class="mt-2 text-center text-xs text-zinc-500">Não tem conta? <a href="{{ route('portal.cadastro') }}" class="link">Cadastre-se</a></p>
                        @php session(['url.intended' => url()->current()]); @endphp
                    @else
                        <form method="POST" action="{{ route('portal.inscricoes.store', $a) }}">
                            @csrf
                            <x-button type="submit" class="w-full" tamanho="lg" icone="ticket">Inscrever-se</x-button>
                        </form>
                    @endif
                </div>
            </div>
        </aside>
    </section>
</x-layouts.portal>
