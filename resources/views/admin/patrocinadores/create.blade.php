<x-layouts.admin titulo="Novo patrocinador">
    <x-cabecalho titulo="Novo patrocinador" :voltar="route('admin.patrocinadores.index')" />
    <form method="POST" action="{{ route('admin.patrocinadores.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @include('admin.patrocinadores._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.patrocinadores.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
