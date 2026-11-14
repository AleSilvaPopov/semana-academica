@props(['name', 'label', 'opcoes' => [], 'selecionados' => [], 'placeholder' => 'Buscar...', 'ajuda' => null])
{{--
    Seleção múltipla com busca, usada nas tabelas N:N sem atributos (tarefa_responsavel, ministra).
    $opcoes: [valor => rótulo]. Envia name[] com os valores escolhidos; o controller faz sync().
--}}
@php
    $lista = collect($opcoes)->map(fn ($r, $v) => ['valor' => (string) $v, 'rotulo' => $r])->values();
    $marcados = array_map('strval', (array) old($name, $selecionados));
    $erros = collect($errors->getMessages())
        ->filter(fn ($m, $k) => $k === $name || str_starts_with($k, $name.'.'))
        ->flatten()
        ->unique();
@endphp
<div x-data="multiSelect(@js($lista), @js($marcados))" {{ $attributes->merge(['class' => 'relative']) }} @click.outside="aberto = false">
    <span class="rotulo">{{ $label }}</span>
    <input type="hidden" name="{{ $name }}_enviado" value="1">
    <template x-for="valor in selecionados" :key="valor">
        <input type="hidden" name="{{ $name }}[]" :value="valor">
    </template>
    <div class="campo flex min-h-[2.6rem] flex-wrap items-center gap-1.5 py-1.5" @click="aberto = true; $refs.busca.focus()">
        <template x-for="valor in selecionados" :key="'chip-' + valor">
            <span class="inline-flex items-center gap-1 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-800 ring-1 ring-brand-200">
                <span x-text="rotuloDe(valor)"></span>
                <button type="button" @click.stop="remover(valor)" class="text-brand-500 hover:text-brand-800" aria-label="Remover">
                    <x-icon name="x" class="size-3.5" />
                </button>
            </span>
        </template>
        <input x-ref="busca" type="text" x-model="busca" @focus="aberto = true" @keydown.escape="aberto = false"
            @keydown.enter.prevent="filtradas.length && adicionar(filtradas[0].valor)"
            placeholder="{{ $placeholder }}" class="min-w-[8rem] flex-1 border-0 p-0.5 text-sm focus:ring-0 focus:outline-none">
    </div>
    <div x-show="aberto" x-cloak x-transition.opacity
        class="absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border border-zinc-200 bg-white py-1 shadow-lg">
        <template x-for="opcao in filtradas" :key="'op-' + opcao.valor">
            <button type="button" @click="adicionar(opcao.valor)" class="block w-full px-3 py-2 text-left text-sm hover:bg-brand-50"
                x-text="opcao.rotulo"></button>
        </template>
        <p x-show="filtradas.length === 0" class="px-3 py-2 text-sm text-zinc-500">Nenhuma opção disponível.</p>
    </div>
    @if ($ajuda)
        <p class="mt-1 text-xs text-zinc-500">{{ $ajuda }}</p>
    @endif
    @foreach ($erros as $erro)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $erro }}</p>
    @endforeach
</div>
