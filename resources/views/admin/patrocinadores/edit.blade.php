<x-layouts.admin titulo="Editar patrocinador">
    <x-cabecalho titulo="Editar patrocinador" :subtitulo="$patrocinador->nome" :voltar="route('admin.patrocinadores.show', $patrocinador)" />
    <form method="POST" action="{{ route('admin.patrocinadores.update', $patrocinador) }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.patrocinadores._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.patrocinadores.show', $patrocinador)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
