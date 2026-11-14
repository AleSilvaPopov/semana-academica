<x-layouts.admin titulo="Novo ministrante">
    <x-cabecalho titulo="Novo ministrante" subtitulo="Escolha um participante já cadastrado." :voltar="route('admin.ministrantes.index')" />
    <form method="POST" action="{{ route('admin.ministrantes.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.ministrantes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.ministrantes.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
