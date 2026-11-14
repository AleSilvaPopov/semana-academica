<x-layouts.simples titulo="Criar conta" class="max-w-2xl">
    <div class="cartao p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-zinc-900">Criar conta de participante</h1>
        <p class="mt-1 text-sm text-zinc-500">Com a conta você se inscreve nas atividades e acessa seus certificados.</p>
        <x-flash class="mt-5" />
        <form method="POST" action="{{ route('portal.cadastro') }}" class="mt-6 space-y-6">
            @csrf
            @include('partials.campos-participante', ['participante' => null, 'editando' => false, 'admin' => false])
            <x-button type="submit" class="w-full" tamanho="lg">Criar conta</x-button>
        </form>
        <p class="mt-6 border-t border-zinc-100 pt-4 text-center text-sm text-zinc-600">
            Já tem conta? <a href="{{ route('portal.login') }}" class="link">Entrar</a>
        </p>
    </div>
</x-layouts.simples>
