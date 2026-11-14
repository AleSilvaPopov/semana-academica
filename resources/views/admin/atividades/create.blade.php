<x-layouts.admin titulo="Nova atividade">
    <x-cabecalho titulo="Nova atividade" :voltar="route('admin.atividades.index')" />
    <form method="POST" action="{{ route('admin.atividades.store') }}" class="space-y-6">
        @csrf
        @include('admin.atividades._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.atividades.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
