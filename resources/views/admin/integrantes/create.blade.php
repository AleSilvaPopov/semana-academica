<x-layouts.admin titulo="Novo integrante">
    <x-cabecalho titulo="Novo integrante" :voltar="route('admin.integrantes.index')" />
    <form method="POST" action="{{ route('admin.integrantes.store') }}" class="space-y-6">
        @csrf
        @include('admin.integrantes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.integrantes.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
