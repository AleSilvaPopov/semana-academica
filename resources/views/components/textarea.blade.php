@props(['name', 'label' => null, 'value' => null, 'ajuda' => null, 'obrigatorio' => false, 'rows' => 3])
@php
    $temErro = $errors->has($name);
    $id = 'campo-'.$name;
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="rotulo">
            {{ $label }} @if ($obrigatorio)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @if ($obrigatorio) required @endif
        {{ $attributes->except('class')->merge(['class' => 'campo'.($temErro ? ' campo-erro' : '')]) }}>{{ old($name, $value) }}</textarea>
    @if ($ajuda)
        <p class="mt-1 text-xs text-zinc-500">{{ $ajuda }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
