@props([
    'name',
    'label' => null,
    'opcoes' => [],
    'selecionado' => null,
    'vazio' => 'Selecione...',
    'ajuda' => null,
    'obrigatorio' => false,
])
{{--
    $opcoes: [valor => rótulo] ou [grupo => [valor => rótulo]] (gera <optgroup>).
    Para FKs, o rótulo é o nome do registro, nunca o ID.
--}}
@php
    $atual = (string) old($name, $selecionado instanceof \BackedEnum ? $selecionado->value : $selecionado);
    $temErro = $errors->has($name);
    $id = $attributes->get('id', 'campo-'.$name);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="rotulo">
            {{ $label }} @if ($obrigatorio)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" @if ($obrigatorio) required @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'campo pr-8'.($temErro ? ' campo-erro' : '')]) }}>
        @if ($vazio !== false)
            <option value="">{{ $vazio }}</option>
        @endif
        @foreach ($opcoes as $valor => $rotulo)
            @if (is_array($rotulo))
                <optgroup label="{{ $valor }}">
                    @foreach ($rotulo as $v => $r)
                        <option value="{{ $v }}" @selected($atual === (string) $v)>{{ $r }}</option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{ $valor }}" @selected($atual === (string) $valor)>{{ $rotulo }}</option>
            @endif
        @endforeach
    </select>
    @if ($ajuda)
        <p class="mt-1 text-xs text-zinc-500">{{ $ajuda }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
