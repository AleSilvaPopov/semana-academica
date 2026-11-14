<x-layouts.simples titulo="Nova senha">
    <div class="cartao p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-zinc-900">Criar nova senha</h1>
        <p class="mt-1 text-sm text-zinc-500">Conta: {{ $participante->email }}</p>
        <x-flash class="mt-5" />
        <form method="POST" action="{{ request()->fullUrl() }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="senha" label="Nova senha" type="password" obrigatorio autofocus ajuda="Mínimo de 6 caracteres." />
            <x-input name="senha_confirmation" label="Confirme a nova senha" type="password" obrigatorio />
            <x-button type="submit" class="w-full">Salvar nova senha</x-button>
        </form>
    </div>
</x-layouts.simples>
