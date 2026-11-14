<x-layouts.admin titulo="Editar patrocínio">
    <x-cabecalho titulo="Editar patrocínio" :subtitulo="$patrocinio->nome_patrocinador.' · '.$comissao->titulo" :voltar="route('admin.patrocinios.index')" />
    <form method="POST" action="{{ route('admin.patrocinios.update', ['comissao' => $comissao, 'patrocinador' => $patrocinador]) }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.patrocinios._form')
        <div class="flex gap-2"><x-button type="submit">Salvar alterações</x-button><x-button variante="secundario" :href="route('admin.patrocinios.index')">Cancelar</x-button></div>
    </form>
</x-layouts.admin>
