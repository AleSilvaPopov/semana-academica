<x-layouts.admin titulo="Editar tarefa">
    <x-cabecalho :titulo="'Editar tarefa #'.$tarefa->id_tarefa" :voltar="route('admin.tarefas.show', $tarefa)" />
    <form method="POST" action="{{ route('admin.tarefas.update', $tarefa) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.tarefas._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.tarefas.show', $tarefa)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
