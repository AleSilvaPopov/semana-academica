<x-layouts.admin titulo="Editar local">
    <x-cabecalho titulo="Editar local" :subtitulo="$local->nome_curto" :voltar="route('admin.locais.show', $local)" />
    <form method="POST" action="{{ route('admin.locais.update', $local) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.locais._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.locais.show', $local)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
