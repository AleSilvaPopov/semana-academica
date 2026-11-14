<x-layouts.admin titulo="Editar ministrante">
    <x-cabecalho titulo="Editar ministrante" :subtitulo="$ministrante->participante->nome_completo" :voltar="route('admin.ministrantes.show', $ministrante)" />
    <form method="POST" action="{{ route('admin.ministrantes.update', $ministrante) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.ministrantes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.ministrantes.show', $ministrante)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
