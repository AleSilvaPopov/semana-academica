<div class="grid gap-6 lg:grid-cols-3">
    <x-secao titulo="Dados da atividade" :descricao="'Edição: '.$edicao->titulo.' ('.$edicao->periodo.')'" class="lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-6">
            <x-input name="nome" label="Nome" :value="$atividade->nome" obrigatorio maxlength="150" class="sm:col-span-6" />
            <x-textarea name="descricao" label="Descrição" :value="$atividade->descricao" obrigatorio class="sm:col-span-6" />
            <x-select name="tipo" label="Tipo" :opcoes="$tipos" :selecionado="$atividade->tipo" obrigatorio class="sm:col-span-3" />
            <x-select name="status" label="Status" :opcoes="$status" :selecionado="$atividade->status" :vazio="false" obrigatorio class="sm:col-span-3" />
            <x-input name="data_realizacao" label="Data e hora" type="datetime-local" :value="$atividade->data_realizacao?->format('Y-m-d\TH:i')" obrigatorio
                min="{{ $edicao->data_inicio->format('Y-m-d') }}T00:00" max="{{ $edicao->data_termino->format('Y-m-d') }}T23:59" class="sm:col-span-3"
                ajuda="Entre {{ $edicao->periodo }}." />
            <x-input name="duracao_minutos" label="Duração (minutos)" type="number" min="1" max="32767" step="5" :value="$atividade->duracao_minutos" obrigatorio class="sm:col-span-3" />
            <x-select name="id_local" label="Local" :opcoes="$locais" :selecionado="$atividade->id_local" obrigatorio class="sm:col-span-6"
                ajuda="As vagas não podem passar da capacidade do local." />
            <x-input name="vagas" label="Vagas" type="number" min="1" step="1" :value="$atividade->vagas" obrigatorio class="sm:col-span-3" />
            <x-input name="valor_inscricao" label="Valor da inscrição (R$)" type="number" min="0" step="0.01" :value="$atividade->valor_inscricao" obrigatorio class="sm:col-span-3"
                ajuda="Use 0 para atividade gratuita." />
        </div>
    </x-secao>
    <div class="space-y-6">
        <x-secao titulo="Organização">
            <x-select name="id_coordenadoria" label="Coordenadoria responsável" :opcoes="$coordenadorias" :selecionado="$atividade->id_coordenadoria" obrigatorio
                ajuda="Coordenadores só podem escolher a própria coordenadoria." />
        </x-secao>
        <x-secao titulo="Ministrantes" descricao="N:N (tabela ministra)">
            <x-multi-select name="ministrantes" label="Quem ministra" :opcoes="$ministrantes"
                :selecionados="$atividade->exists ? $atividade->ministrantes->pluck('cpf')->all() : []" placeholder="Buscar ministrante..."
                ajuda="Não encontrou? Cadastre em Ministrantes." />
        </x-secao>
    </div>
</div>
