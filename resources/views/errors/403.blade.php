<x-layouts.simples titulo="Acesso negado">
    <div class="cartao p-8 text-center">
        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-brand-50 text-brand-600">
            <x-icon name="lock" class="size-7" />
        </div>
        <p class="text-sm font-semibold text-brand-600">Erro 403</p>
        <h1 class="mt-1 text-2xl font-semibold text-zinc-900">Acesso negado</h1>
        <p class="mt-2 text-sm text-zinc-600">{{ $exception?->getMessage() && 403 == 403 && $exception->getMessage() !== 'This action is unauthorized.' ? $exception->getMessage() : 'Você não tem permissão para acessar esta página ou executar esta ação.' }}</p>
        <div class="mt-6 flex justify-center gap-2">
            <x-button variante="secundario" href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" icone="arrow-left">Voltar</x-button>
            <x-button href="{{ url('/') }}">Página inicial</x-button>
        </div>
    </div>
</x-layouts.simples>
