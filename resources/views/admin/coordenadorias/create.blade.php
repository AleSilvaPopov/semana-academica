<x-layouts.admin titulo="Nova coordenadoria">
    <x-cabecalho titulo="Nova coordenadoria" :voltar="route('admin.coordenadorias.index')" />
    <form method="POST" action="{{ route('admin.coordenadorias.store') }}" class="max-w-4xl space-y-6">
        @csrf
        @include('admin.coordenadorias._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.coordenadorias.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
