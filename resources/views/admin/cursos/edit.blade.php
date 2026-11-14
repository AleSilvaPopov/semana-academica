<x-layouts.admin titulo="Editar curso">
    <x-cabecalho titulo="Editar curso" :subtitulo="$curso->nome_curso" :voltar="route('admin.cursos.show', $curso)" />
    <form method="POST" action="{{ route('admin.cursos.update', $curso) }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.cursos._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.cursos.show', $curso)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
