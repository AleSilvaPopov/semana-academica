@php $editando = $integrante->exists; @endphp
<div class="grid gap-6 lg:grid-cols-3">
    <x-secao titulo="Dados do integrante" class="lg:col-span-2">
        <div class="grid gap-4 sm:grid-cols-2">
            @if ($editando)
                <x-input name="rga_exibicao" label="RGA" :value="$integrante->rga" disabled ajuda="O RGA é a chave do integrante e não pode ser alterado." />
            @else
                <x-input name="rga" label="RGA" obrigatorio maxlength="20" inputmode="numeric" placeholder="Ex.: 2026113855" />
            @endif
            <x-input name="cpf" label="CPF" :value="$integrante->cpf ? formata_cpf($integrante->cpf) : null" obrigatorio x-data x-mask="999.999.999-99" inputmode="numeric" placeholder="000.000.000-00" />
            <x-input name="nome_completo" label="Nome completo" :value="$integrante->nome_completo" obrigatorio maxlength="150" class="sm:col-span-2" />
            <x-select name="id_curso" label="Curso" :opcoes="$cursos" :selecionado="$integrante->id_curso" obrigatorio />
            <x-input name="funcao" label="Função na comissão" :value="$integrante->funcao" obrigatorio maxlength="60" placeholder="Ex.: Apoio logístico" />
            <x-input name="data_ingresso" label="Data de ingresso" type="date" :value="$integrante->data_ingresso?->format('Y-m-d')" obrigatorio max="{{ today()->format('Y-m-d') }}" />
            <x-select name="id_coordenadoria" label="Coordenadoria em que atua" :opcoes="$coordenadorias" :selecionado="$integrante->id_coordenadoria"
                vazio="Nenhuma (participação opcional)" ajuda="Relação integrante → coordenadoria (guarda só a atuação mais recente)." />
            <x-textarea name="observacoes" label="Observações" :value="$integrante->observacoes" class="sm:col-span-2" />
        </div>
    </x-secao>

    <div class="space-y-6">
        <x-secao titulo="Contato" descricao="Atributos multivalorados">
            <div class="space-y-5">
                <x-lista-dinamica name="telefones" label="Telefones" :itens="$editando ? $integrante->telefones->pluck('telefone')->all() : []"
                    tipo="tel" mascara="telefone" placeholder="(67) 99999-9999" textoAdicionar="Adicionar" />
                <x-lista-dinamica name="emails" label="E-mails" :itens="$editando ? $integrante->emails->pluck('email')->all() : []"
                    tipo="email" placeholder="nome@academico.ufgd.edu.br" maxlength="120" textoAdicionar="Adicionar" />
            </div>
        </x-secao>
        <x-secao titulo="Senha de acesso ao painel">
            <div class="space-y-4">
                <x-input name="senha" :label="$editando ? 'Nova senha' : 'Senha'" type="password" :obrigatorio="! $editando" autocomplete="new-password"
                    :ajuda="$editando ? 'Deixe em branco para manter a senha atual.' : 'Mínimo de 6 caracteres. Gravada como SHA-256.'" />
                <x-input name="senha_confirmation" label="Confirme a senha" type="password" :obrigatorio="! $editando" autocomplete="new-password" />
            </div>
        </x-secao>
    </div>
</div>
