<x-layouts.admin titulo="Editar participante">
    <x-cabecalho titulo="Editar participante" :subtitulo="$participante->nome_completo" :voltar="route('admin.participantes.show', $participante)" />
    <form method="POST" action="{{ route('admin.participantes.update', $participante) }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')
        <x-secao titulo="Dados do participante">
            @include('partials.campos-participante', ['participante' => $participante, 'editando' => true, 'admin' => true])
        </x-secao>
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.participantes.show', $participante)">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
