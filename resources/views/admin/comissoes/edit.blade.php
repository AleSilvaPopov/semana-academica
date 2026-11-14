<x-layouts.admin titulo="Editar edição">
    <x-cabecalho titulo="Editar edição" :subtitulo="$comissao->titulo" :voltar="route('admin.comissoes.show', $comissao)" />
    <form method="POST" action="{{ route('admin.comissoes.update', $comissao) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.comissoes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.comissoes.show', $comissao)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
