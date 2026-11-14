<x-layouts.admin titulo="Novo patrocínio">
    <x-cabecalho titulo="Novo patrocínio" :voltar="route('admin.patrocinios.index')" />
    @if ($patrocinadores->isEmpty())
        <x-alert tipo="aviso" class="mb-4">Todos os patrocinadores já estão nesta edição. <a href="{{ route('admin.patrocinadores.create') }}" class="underline">Cadastre um novo patrocinador</a>.</x-alert>
    @endif
    <form method="POST" action="{{ route('admin.patrocinios.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @include('admin.patrocinios._form')
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.patrocinios.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
