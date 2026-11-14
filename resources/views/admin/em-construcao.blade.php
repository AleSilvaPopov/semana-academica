<x-layouts.admin :titulo="$titulo">
    <x-cabecalho :titulo="$titulo" subtitulo="Edição selecionada: {{ $edicaoAtual?->titulo ?? '—' }}" />
    <div class="cartao flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-4 rounded-full bg-brand-50 p-3 text-brand-600"><x-icon name="clock" class="size-7" /></div>
        <h2 class="font-semibold text-zinc-900">Tela em construção</h2>
        <p class="mt-1 max-w-md text-sm text-zinc-500">A estrutura, o layout e a navegação já funcionam. O conteúdo desta tela é entregue nas próximas partes do projeto.</p>
    </div>
</x-layouts.admin>
