<x-layouts.admin titulo="Novo participante">
    <x-cabecalho titulo="Novo participante" :voltar="route('admin.participantes.index')" />
    <form method="POST" action="{{ route('admin.participantes.store') }}" class="max-w-4xl space-y-6">
        @csrf
        <x-secao titulo="Dados do participante">
            @include('partials.campos-participante', ['participante' => null, 'editando' => false, 'admin' => true])
        </x-secao>
        <div class="flex gap-2"><x-button type="submit">Salvar</x-button><x-button variante="secundario" :href="route('admin.participantes.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
