<x-layouts.simples titulo="Painel da organização">
    <div class="cartao p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-zinc-900">Painel da organização</h1>
        <p class="mt-1 text-sm text-zinc-500">Acesso dos integrantes da comissão organizadora.</p>

        <x-flash class="mt-5" />

        <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="rga" label="RGA" :value="old('rga')" obrigatorio autofocus autocomplete="username" inputmode="numeric" placeholder="Ex.: 2023104791" />
            <x-input name="senha" label="Senha" type="password" obrigatorio autocomplete="current-password" />
            <x-button type="submit" class="w-full" tamanho="lg">Entrar</x-button>
        </form>

        <p class="mt-6 border-t border-zinc-100 pt-4 text-center text-xs text-zinc-500">
            Esqueceu a senha? Peça ao presidente da edição para redefinir no cadastro de integrantes.
        </p>
    </div>
    <p class="mt-4 text-center text-sm text-zinc-500">
        <a href="{{ route('portal.home') }}" class="link">← Voltar ao portal do participante</a>
    </p>
</x-layouts.simples>
