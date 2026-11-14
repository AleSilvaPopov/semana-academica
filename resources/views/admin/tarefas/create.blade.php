<x-layouts.admin titulo="Nova tarefa">
    <x-cabecalho titulo="Nova tarefa" :voltar="route('admin.tarefas.index')" />
    <form method="POST" action="{{ route('admin.tarefas.store') }}" class="space-y-6">
        @csrf
        @include('admin.tarefas._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.tarefas.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
