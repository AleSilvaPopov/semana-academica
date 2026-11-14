<x-secao titulo="Dados do curso">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input name="nome_curso" label="Nome do curso" :value="$curso->nome_curso" obrigatorio maxlength="100" autofocus />
        <x-input name="faculdade" label="Faculdade" :value="$curso->faculdade" obrigatorio maxlength="150" list="lista-faculdades" />
        <datalist id="lista-faculdades">
            @foreach ($faculdades as $f)<option value="{{ $f }}">@endforeach
        </datalist>
    </div>
</x-secao>
