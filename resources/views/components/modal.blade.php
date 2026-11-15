@props(['nome', 'titulo' => null, 'largura' => 'max-w-md'])
{{-- Abrir com: $dispatch('abrir-modal', 'nome') | fechar com: $dispatch('fechar-modal', 'nome') --}}
<div x-data="{ aberto: false }" x-cloak
    @abrir-modal.window="if ($event.detail === '{{ $nome }}') aberto = true"
    @fechar-modal.window="if ($event.detail === '{{ $nome }}') aberto = false"
    @keydown.escape.window="aberto = false">
    <div x-show="aberto" x-transition.opacity class="fixed inset-0 z-50 bg-black/60 backdrop-blur-[1px]"></div>
    <div x-show="aberto" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div @click.outside="aberto = false" class="w-full {{ $largura }} rounded-xl bg-superficie shadow-xl">
            @if ($titulo)
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-zinc-900">{{ $titulo }}</h3>
                    <button type="button" @click="aberto = false" class="text-zinc-400 hover:text-zinc-600" aria-label="Fechar">
                        <x-icon name="x" class="size-5" />
                    </button>
                </div>
            @endif
            <div class="px-5 py-4 text-sm text-zinc-600">{{ $slot }}</div>
            @isset($rodape)
                <div class="flex justify-end gap-2 rounded-b-xl bg-zinc-50 px-5 py-3">{{ $rodape }}</div>
            @endisset
        </div>
    </div>
</div>
