@props(['action', 'descricao' => 'este registro', 'rotulo' => 'Excluir', 'tamanho' => 'sm', 'icone' => true, 'texto' => null])
{{-- Botão de exclusão com modal de confirmação (DELETE em $action). --}}
@php $nomeModal = 'excluir-'.md5($action); @endphp
<x-button variante="fantasma" :tamanho="$tamanho" class="text-red-600 hover:bg-red-50 hover:text-red-700"
    x-data x-on:click="$dispatch('abrir-modal', '{{ $nomeModal }}')" :icone="$icone ? 'trash' : null" title="{{ $rotulo }}">
    @if ($texto){{ $texto }}@endif
</x-button>
<x-modal :nome="$nomeModal" titulo="Confirmar exclusão">
    <div class="flex items-start gap-3 text-left">
        <div class="rounded-full bg-red-100 p-2 text-red-600"><x-icon name="warning" class="size-5" /></div>
        <p class="pt-1 whitespace-normal">Tem certeza de que deseja excluir <strong class="text-zinc-900">{{ $descricao }}</strong>? Esta ação não pode ser desfeita.</p>
    </div>
    <x-slot:rodape>
        <x-button variante="secundario" x-on:click="$dispatch('fechar-modal', '{{ $nomeModal }}')">Cancelar</x-button>
        <form method="POST" action="{{ $action }}">
            @csrf
            @method('DELETE')
            <x-button type="submit" variante="perigo">{{ $rotulo }}</x-button>
        </form>
    </x-slot:rodape>
</x-modal>
