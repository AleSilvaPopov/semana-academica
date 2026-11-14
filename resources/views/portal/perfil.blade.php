<x-layouts.portal titulo="Meu perfil">
    <section class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Meu perfil</h1>
        <p class="mt-1 text-sm text-zinc-500">Mantenha seus dados atualizados: o nome sai no certificado.</p>

        <form method="POST" action="{{ route('portal.perfil.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="cartao p-5 sm:p-6">
                @include('partials.campos-participante', ['participante' => $participante, 'editando' => true, 'admin' => false])
                <div class="mt-4 sm:w-1/2 sm:pr-2">
                    <x-input name="senha_atual" label="Senha atual" type="password" autocomplete="current-password" ajuda="Necessária apenas para trocar a senha." />
                </div>
            </div>
            <x-button type="submit" tamanho="lg">Salvar alterações</x-button>
        </form>
    </section>
</x-layouts.portal>
