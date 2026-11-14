<x-layouts.simples titulo="Entrar">
    <div class="cartao p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-zinc-900">Entrar no portal</h1>
        <p class="mt-1 text-sm text-zinc-500">Acesse para se inscrever nas atividades e baixar seus certificados.</p>
        <x-flash class="mt-5" />
        <form method="POST" action="{{ route('portal.login') }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="email" label="E-mail" type="email" :value="old('email')" obrigatorio autofocus autocomplete="email" />
            <x-input name="senha" label="Senha" type="password" obrigatorio autocomplete="current-password" />
            <div class="text-right text-sm"><a href="{{ route('portal.senha.esqueci') }}" class="link">Esqueci minha senha</a></div>
            <x-button type="submit" class="w-full" tamanho="lg">Entrar</x-button>
        </form>
        <p class="mt-6 border-t border-zinc-100 pt-4 text-center text-sm text-zinc-600">
            Ainda não tem conta? <a href="{{ route('portal.cadastro') }}" class="link">Criar conta</a>
        </p>
    </div>
    <p class="mt-4 text-center text-sm text-zinc-500"><a href="{{ route('portal.home') }}" class="link">← Voltar ao início</a></p>
</x-layouts.simples>
