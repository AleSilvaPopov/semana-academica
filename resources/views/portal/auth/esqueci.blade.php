<x-layouts.simples titulo="Esqueci minha senha">
    <div class="cartao p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-zinc-900">Esqueci minha senha</h1>
        <p class="mt-1 text-sm text-zinc-500">Informe o e-mail da sua conta. Enviaremos um link para criar uma nova senha.</p>
        <x-flash class="mt-5" />
        @if (session('link_dev'))
            <x-alert tipo="aviso" titulo="Modo de desenvolvimento" class="mt-3">
                O e-mail foi gravado em storage/logs/laravel.log. Para testar, use o link:
                <a href="{{ session('link_dev') }}" class="mt-1 block break-all underline">{{ session('link_dev') }}</a>
            </x-alert>
        @endif
        <form method="POST" action="{{ route('portal.senha.esqueci') }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="email" label="E-mail" type="email" :value="old('email')" obrigatorio autofocus />
            <x-button type="submit" class="w-full">Enviar link</x-button>
        </form>
    </div>
    <p class="mt-4 text-center text-sm text-zinc-500"><a href="{{ route('portal.login') }}" class="link">← Voltar ao login</a></p>
</x-layouts.simples>
