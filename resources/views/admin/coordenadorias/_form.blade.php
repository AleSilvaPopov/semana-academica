<x-secao titulo="Dados da coordenadoria" :descricao="'Edição: '.$edicao->titulo">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input name="nome" label="Nome" :value="$coordenadoria->nome" obrigatorio maxlength="80" placeholder="Ex.: Infraestrutura" />
        <x-select name="rga_coordenador" label="Coordenador(a)" :opcoes="$integrantes" :selecionado="$coordenadoria->rga_coordenador" obrigatorio
            ajuda="A coordenação não pode começar antes do ingresso do integrante na comissão." />
        <x-textarea name="descricao_atuacao" label="Descrição da atuação" :value="$coordenadoria->descricao_atuacao" obrigatorio class="sm:col-span-2" />
        <x-input name="data_inicio_coordenacao" label="Início da coordenação" type="date" :value="$coordenadoria->data_inicio_coordenacao?->format('Y-m-d')" obrigatorio />
        <x-input name="data_termino_coordenacao" label="Término da coordenação" type="date" :value="$coordenadoria->data_termino_coordenacao?->format('Y-m-d')"
            ajuda="Opcional. Deixe em branco se a coordenação está em andamento." />
    </div>
</x-secao>
