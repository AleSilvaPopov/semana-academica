<div class="grid gap-6 lg:grid-cols-3">
    <x-secao titulo="Dados da tarefa" class="lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-6">
            <x-textarea name="descricao" label="Descrição" :value="$tarefa->descricao" obrigatorio rows="4" class="sm:col-span-6" />
            <x-input name="data_criacao" label="Data de criação" type="date" :value="$tarefa->data_criacao?->format('Y-m-d')" obrigatorio class="sm:col-span-2" />
            <x-input name="prazo_final" label="Prazo final" type="date" :value="$tarefa->prazo_final?->format('Y-m-d')" obrigatorio class="sm:col-span-2" />
            <x-select name="status" label="Status" :opcoes="$statusOpcoes" :selecionado="$tarefa->status" :vazio="false" obrigatorio class="sm:col-span-2" />
        </div>
    </x-secao>
    <div class="space-y-6">
        <x-secao titulo="Criador" descricao="Só coordenadores criam tarefas (trigger trg_tarefa_criador_bi/bu)">
            @if ($criadores->isEmpty())
                <x-alert tipo="aviso">Esta edição não tem coordenadores. Cadastre as coordenadorias primeiro.</x-alert>
            @elseif ($criadorFixo)
                <x-input name="criador_exibicao" label="Criador" :value="$criadores->first()" disabled ajuda="Coordenadores criam tarefas em nome próprio." />
                <input type="hidden" name="rga_criador" value="{{ $criadores->keys()->first() }}">
                @error('rga_criador')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            @else
                <x-select name="rga_criador" label="Coordenador(a) criador(a)" :opcoes="$criadores" :selecionado="$tarefa->rga_criador" obrigatorio />
            @endif
        </x-secao>
        <x-secao titulo="Responsáveis" descricao="N:N (tabela tarefa_responsavel)">
            <x-multi-select name="responsaveis" label="Integrantes responsáveis" :opcoes="$integrantes"
                :selecionados="$tarefa->exists ? $tarefa->responsaveis->pluck('rga')->all() : []" placeholder="Buscar integrante..." />
        </x-secao>
    </div>
</div>
