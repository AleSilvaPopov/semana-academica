<x-layouts.admin titulo="Editar coordenadoria">
    <x-cabecalho titulo="Editar coordenadoria" :subtitulo="$coordenadoria->nome" :voltar="route('admin.coordenadorias.show', $coordenadoria)" />
    <form method="POST" action="{{ route('admin.coordenadorias.update', $coordenadoria) }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.coordenadorias._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.coordenadorias.show', $coordenadoria)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
