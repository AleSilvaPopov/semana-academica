<x-layouts.admin titulo="Editar atividade">
    <x-cabecalho titulo="Editar atividade" :subtitulo="$atividade->nome" :voltar="route('admin.atividades.show', $atividade)" />
    <form method="POST" action="{{ route('admin.atividades.update', $atividade) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.atividades._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.atividades.show', $atividade)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
