{{-- Cartão de atividade (página inicial e programação). Variáveis: $atividade, $inscrito (bool) --}}
@php
    $a = $atividade;
    $restantes = $a->vagas_restantes;
    $inscrito = $inscrito ?? false;
@endphp
<a href="{{ route('portal.atividades.show', $a) }}"
    class="glass-card group flex h-full flex-col p-4 hover:-translate-y-0.5">
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-wrap items-center gap-1.5">
            <x-badge :enum="$a->tipo" />
            @if ($a->status === \App\Enums\StatusAtividade::Cancelada)
                <x-badge cor="red">Cancelada</x-badge>
            @elseif ($a->status === \App\Enums\StatusAtividade::Planejada)
                <x-badge cor="amber">A confirmar</x-badge>
            @endif
            @if ($inscrito)<x-badge cor="green">Inscrito</x-badge>@endif
        </div>
        <span class="shrink-0 font-mono text-sm text-neon-soft tabular-nums">{{ $a->data_realizacao->format('H:i') }}</span>
    </div>
    <h3 class="mt-2 font-display text-lg leading-snug font-semibold text-zinc-950 group-hover:text-brand-700">{{ $a->nome }}</h3>
    <p class="mt-1 line-clamp-2 text-sm text-zinc-500">{{ $a->descricao }}</p>

    <div class="mt-auto pt-4">
        @if ($a->ministrantes->isNotEmpty())
            <div class="mb-3 flex items-center gap-2">
                <div class="flex -space-x-2">
                    @foreach ($a->ministrantes->take(3) as $m)
                        <x-avatar :nome="$m->participante->nome_completo" :url="$m->imagem_url" tamanho="size-8" class="text-xs" />
                    @endforeach
                </div>
                <p class="truncate text-xs text-zinc-600">{{ $a->ministrantes->map(fn ($m) => $m->participante->nome_completo)->join(', ') }}</p>
            </div>
        @endif
        <div class="flex items-center justify-between gap-2 border-t border-zinc-100 pt-3 text-xs text-zinc-500">
            <span class="flex items-center gap-1"><x-icon name="clock" class="size-4" /> {{ formata_duracao($a->duracao_minutos) }}</span>
            <span class="flex items-center gap-1 truncate"><x-icon name="map" class="size-4" /> {{ $a->local?->nome_curto }}</span>
            @if ($a->status !== \App\Enums\StatusAtividade::Cancelada)
                <span class="font-medium {{ $restantes === 0 ? 'text-red-600' : ($restantes <= 5 ? 'text-amber-700' : 'text-emerald-700') }}">
                    {{ $restantes === 0 ? 'Esgotada' : $restantes.' vaga'.($restantes > 1 ? 's' : '') }}
                </span>
            @endif
        </div>
    </div>
</a>
