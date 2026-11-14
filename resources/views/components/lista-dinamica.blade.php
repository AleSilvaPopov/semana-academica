@props([
    'name',
    'label',
    'itens' => [],
    'tipo' => 'text',
    'placeholder' => '',
    'maxlength' => null,
    'mascara' => null,
    'minimo' => 0,
    'textoAdicionar' => 'Adicionar',
    'ajuda' => null,
])
{{--
    Lista dinâmica (Alpine.js) para atributos multivalorados: cada linha vira name[] no formulário.
    No back-end os itens antigos são apagados e os novos reinseridos dentro de uma transação.
    mascara="telefone" aplica a máscara (99) 99999-9999 (só visual; o servidor remove).
--}}
@php
    $valores = old($name, $itens);
    $valores = is_array($valores) ? array_values($valores) : [];
    if ($mascara === 'telefone') {
        $valores = array_map(fn ($v) => formata_telefone((string) $v), $valores);
    }
    $erros = collect($errors->getMessages())
        ->filter(fn ($m, $k) => $k === $name || str_starts_with($k, $name.'.'))
        ->flatten()
        ->unique();
@endphp
<div x-data="listaDinamica(@js($valores), {{ (int) $minimo }})" {{ $attributes->merge(['class' => '']) }}>
    <div class="mb-1 flex items-center justify-between">
        <span class="rotulo mb-0">{{ $label }}</span>
        <button type="button" @click="adicionar()" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-900">
            <x-icon name="plus" class="size-4" /> {{ $textoAdicionar }}
        </button>
    </div>
    <div class="space-y-2">
        <template x-for="(item, indice) in itens" :key="indice">
            <div class="flex items-center gap-2">
                <input data-item type="{{ $tipo }}" name="{{ $name }}[]" x-model="item.valor" placeholder="{{ $placeholder }}"
                    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                    @if ($mascara === 'telefone') x-mask:dynamic="mascaraTelefone" inputmode="numeric" @endif
                    class="campo">
                <button type="button" @click="remover(indice)" class="rounded-lg p-2 text-zinc-400 hover:bg-red-50 hover:text-red-600" title="Remover">
                    <x-icon name="trash" class="size-4" />
                </button>
            </div>
        </template>
        <p x-show="itens.length === 0" class="rounded-lg border border-dashed border-zinc-300 px-3 py-2 text-xs text-zinc-500">
            Nenhum item. Clique em "{{ $textoAdicionar }}".
        </p>
    </div>
    @if ($ajuda)
        <p class="mt-1 text-xs text-zinc-500">{{ $ajuda }}</p>
    @endif
    @foreach ($erros as $erro)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $erro }}</p>
    @endforeach
</div>
