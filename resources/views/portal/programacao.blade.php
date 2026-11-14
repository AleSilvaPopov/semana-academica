<x-layouts.portal titulo="Programação">
    <section class="border-b border-zinc-200 bg-zinc-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl">Programação</h1>
            @if ($edicao)
                <p class="mt-1 text-zinc-500">{{ $edicao->nome_completo }} · {{ $edicao->periodo }}</p>
            @endif
            <form method="GET" class="mt-5 flex flex-col gap-2 sm:flex-row">
                <select name="edicao" class="campo sm:w-64" onchange="this.form.submit()" aria-label="Edição">
                    @foreach ($edicoes as $ed)
                        <option value="{{ $ed->id_comissao }}" @selected($edicao?->id_comissao === $ed->id_comissao)>{{ $ed->titulo }}</option>
                    @endforeach
                </select>
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ request()->fullUrlWithQuery(['tipo' => null]) }}" class="rounded-full px-3.5 py-2 text-sm font-medium {{ ! request('tipo') ? 'bg-brand-600 text-white' : 'bg-white text-zinc-600 ring-1 ring-zinc-200 hover:bg-zinc-100' }}">Todas</a>
                    @foreach ($tipos as $valor => $rotulo)
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => $valor]) }}" class="rounded-full px-3.5 py-2 text-sm font-medium {{ request('tipo') === $valor ? 'bg-brand-600 text-white' : 'bg-white text-zinc-600 ring-1 ring-zinc-200 hover:bg-zinc-100' }}">{{ $rotulo }}s</a>
                    @endforeach
                </div>
                <noscript><x-button type="submit" variante="secundario">Ver</x-button></noscript>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @forelse ($dias as $dia => $atividadesDoDia)
            @php $data = \Illuminate\Support\Carbon::parse($dia); @endphp
            <div class="mb-10">
                <div class="sticky top-16 z-10 -mx-4 mb-4 flex items-baseline gap-3 bg-white/95 px-4 py-2 backdrop-blur sm:mx-0 sm:px-0">
                    <h2 class="text-lg font-semibold text-zinc-900 capitalize">{{ $data->translatedFormat('l') }}</h2>
                    <span class="text-sm text-zinc-500">{{ $data->translatedFormat('d \d\e F') }} · {{ $atividadesDoDia->count() }} atividade(s)</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($atividadesDoDia as $atividade)
                        @include('portal._cartao-atividade', ['atividade' => $atividade, 'inscrito' => in_array($atividade->id_atividade, $minhas)])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-16 text-center">
                <x-icon name="calendar" class="mx-auto size-8 text-zinc-400" />
                <p class="mt-3 font-medium text-zinc-700">Nenhuma atividade encontrada.</p>
                <p class="text-sm text-zinc-500">Tente outro tipo de atividade ou outra edição.</p>
            </div>
        @endforelse
    </section>
</x-layouts.portal>
