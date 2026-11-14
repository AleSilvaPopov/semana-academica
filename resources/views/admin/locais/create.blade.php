<x-layouts.admin titulo="Novo local">
    <x-cabecalho titulo="Novo local" :voltar="route('admin.locais.index')" />
    <form method="POST" action="{{ route('admin.locais.store') }}" class="space-y-6">
        @csrf
        @include('admin.locais._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.locais.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
