<div class="grid gap-6 lg:grid-cols-3">
    <x-secao titulo="Dados do local" class="lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-select name="tipo" label="Tipo" :opcoes="$tipos" :selecionado="$local->tipo" obrigatorio />
            <x-input name="capacidade" label="Capacidade (lugares)" type="number" min="1" step="1" :value="$local->capacidade" obrigatorio />
            <x-input name="faculdade" label="Faculdade" :value="$local->faculdade" obrigatorio maxlength="150" list="lista-faculdades" class="sm:col-span-2" />
            <datalist id="lista-faculdades">@foreach ($faculdades as $f)<option value="{{ $f }}">@endforeach</datalist>
            <x-input name="bloco" label="Bloco" :value="$local->bloco" obrigatorio maxlength="20" placeholder="Ex.: FACET" />
            <x-input name="numero" label="Número / identificação" :value="$local->numero" obrigatorio maxlength="10" placeholder="Ex.: LAB-101" />
            <x-textarea name="observacoes" label="Observações" :value="$local->observacoes" class="sm:col-span-2" />
        </div>
    </x-secao>
    <x-secao titulo="Equipamentos" descricao="Atributo multivalorado (local_equipamento)">
        <x-lista-dinamica name="equipamentos" label="Equipamentos disponíveis" :itens="$local->exists ? $local->equipamentos->pluck('equipamento')->all() : []"
            placeholder="Ex.: projetor" maxlength="60" textoAdicionar="Adicionar" />
    </x-secao>
</div>
