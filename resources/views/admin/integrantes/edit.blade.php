<x-layouts.admin titulo="Editar integrante">
    <x-cabecalho titulo="Editar integrante" :subtitulo="$integrante->nome_completo" :voltar="route('admin.integrantes.show', $integrante)" />
    <form method="POST" action="{{ route('admin.integrantes.update', $integrante) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.integrantes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.integrantes.show', $integrante)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
