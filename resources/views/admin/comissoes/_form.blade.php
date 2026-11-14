<div class="grid gap-6 lg:grid-cols-3">
    <x-secao titulo="Dados da edição" class="lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-6">
            <x-input name="nome_evento" label="Nome do evento" :value="$comissao->nome_evento" obrigatorio maxlength="150" class="sm:col-span-4" />
            <x-input name="edicao" label="Nº da edição" type="number" min="1" step="1" :value="$comissao->edicao" obrigatorio class="sm:col-span-2" />
            <x-input name="data_inicio" label="Data de início" type="date" :value="$comissao->data_inicio?->format('Y-m-d')" obrigatorio class="sm:col-span-3" />
            <x-input name="data_termino" label="Data de término" type="date" :value="$comissao->data_termino?->format('Y-m-d')" obrigatorio class="sm:col-span-3" />
            <x-input name="local_sede" label="Local sede" :value="$comissao->local_sede" obrigatorio maxlength="150" class="sm:col-span-6" />
            <x-textarea name="observacoes" label="Observações" :value="$comissao->observacoes" class="sm:col-span-6" />
        </div>
    </x-secao>
    <x-secao titulo="Presidência" descricao="Tabela presidencia: uma linha por edição">
        <div class="space-y-4">
            <x-select name="rga_presidente" label="Presidente" :opcoes="$integrantes" :selecionado="$comissao->presidencia?->rga_presidente" obrigatorio />
            <x-select name="rga_vice" label="Vice-presidente" :opcoes="$integrantes" :selecionado="$comissao->presidencia?->rga_vice" obrigatorio
                ajuda="Precisa ser uma pessoa diferente do presidente." />
        </div>
    </x-secao>
</div>
