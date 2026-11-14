<x-secao titulo="Patrocínio" :descricao="'Edição: '.$comissao->titulo">
    <div class="grid gap-4 sm:grid-cols-2">
        @if ($patrocinio->exists)
            <x-input name="patrocinador_exibicao" label="Patrocinador" :value="$patrocinio->nome_patrocinador" disabled
                ajuda="Patrocinador e edição formam a chave do patrocínio e não mudam." />
        @else
            <x-select name="nome_patrocinador" label="Patrocinador" :opcoes="$patrocinadores" obrigatorio
                vazio="Selecione..." ajuda="Só aparecem patrocinadores que ainda não apoiam esta edição." />
        @endif
        <x-select name="tipo_patrocinio" label="Tipo" :opcoes="$tipos" :selecionado="$patrocinio->tipo_patrocinio" obrigatorio />
        <x-input name="descricao_patrocinio" label="Descrição" :value="$patrocinio->descricao_patrocinio" obrigatorio maxlength="255" placeholder="Ex.: Cota ouro" class="sm:col-span-2" />
        <div>
            <label for="campo-valor" class="rotulo">Valor (R$)</label>
            <div class="relative">
                <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-sm text-zinc-500">R$</span>
                <input id="campo-valor" type="number" name="valor" min="0" step="0.01" value="{{ old('valor', $patrocinio->valor) }}"
                    class="campo pl-10 @error('valor') campo-erro @enderror" placeholder="0,00">
            </div>
            <p class="mt-1 text-xs text-zinc-500">Opcional: deixe vazio para patrocínio em serviço, material ou equipamento.</p>
            @error('valor')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>
        <x-textarea name="requisitos" label="Requisitos / contrapartidas" :value="$patrocinio->requisitos" class="sm:col-span-2" />
    </div>
</x-secao>
