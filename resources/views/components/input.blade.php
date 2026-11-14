@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'ajuda' => null,
    'obrigatorio' => false,
])
@php
    // Converte "telefones.0" <-> "telefones[0]" para old() e mensagens de erro.
    $chave = str_replace(['[', ']'], ['.', ''], $name);
    $valor = $type === 'password' ? null : old($chave, $value);
    $temErro = $errors->has($chave);
    $id = $attributes->get('id', 'campo-'.str_replace('.', '-', $chave));
@endphp
<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if ($label)
        <label for="{{ $id }}" class="rotulo">
            {{ $label }} @if ($obrigatorio)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $valor }}"
        @if ($obrigatorio) required @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'campo'.($temErro ? ' campo-erro' : '')]) }}>
    @if ($ajuda)
        <p class="mt-1 text-xs text-zinc-500">{{ $ajuda }}</p>
    @endif
    @error($chave)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
