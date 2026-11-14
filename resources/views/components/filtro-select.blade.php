@props(['name', 'opcoes' => [], 'todos' => 'Todos'])
<select name="{{ $name }}" {{ $attributes->merge(['class' => 'campo lg:w-48']) }} aria-label="{{ $todos }}">
    <option value="">{{ $todos }}</option>
    @foreach ($opcoes as $valor => $rotulo)
        <option value="{{ $valor }}" @selected((string) request($name) === (string) $valor)>{{ $rotulo }}</option>
    @endforeach
</select>
