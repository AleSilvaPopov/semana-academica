<x-layouts.admin titulo="Novo curso">
    <x-cabecalho titulo="Novo curso" :voltar="route('admin.cursos.index')" />
    <form method="POST" action="{{ route('admin.cursos.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @include('admin.cursos._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.cursos.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
