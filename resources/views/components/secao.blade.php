@props(['titulo' => null, 'descricao' => null])
{{-- Cartão com título, usado para agrupar campos de formulário e blocos de detalhes. --}}
<section {{ $attributes->merge(['class' => 'cartao']) }}>
    @if ($titulo)
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-3.5">
            <div>
                <h2 class="text-sm font-semibold text-zinc-900">{{ $titulo }}</h2>
                @if ($descricao)
                    <p class="text-xs text-zinc-500">{{ $descricao }}</p>
                @endif
            </div>
            @isset($acoes)
                <div class="flex items-center gap-2">{{ $acoes }}</div>
            @endisset
        </div>
    @endif
    <div class="p-5">{{ $slot }}</div>
</section>
