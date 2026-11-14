<x-layouts.admin titulo="Nova edição">
    <x-cabecalho titulo="Nova edição" :voltar="route('admin.comissoes.index')" />
    <form method="POST" action="{{ route('admin.comissoes.store') }}" class="space-y-6">
        @csrf
        @include('admin.comissoes._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.comissoes.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
