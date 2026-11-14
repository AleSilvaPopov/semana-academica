{{--
    Campos do participante, compartilhados por: cadastro do portal, "Meu perfil" e o CRUD do painel.
    Variáveis: $participante (ou null), $editando (bool), $admin (bool: mostra status da conta)
--}}
@php
    $p = $participante ?? null;
    $editando = $editando ?? false;
    $admin = $admin ?? false;
@endphp
<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="nome_completo" label="Nome completo" :value="$p?->nome_completo" obrigatorio maxlength="150" class="sm:col-span-2" />

    @if ($editando)
        <x-input name="cpf_exibicao" label="CPF" :value="formata_cpf($p->cpf)" disabled
            ajuda="O CPF identifica o cadastro e não pode ser alterado." />
    @else
        <x-input name="cpf" label="CPF" :value="old('cpf')" obrigatorio x-data x-mask="999.999.999-99" inputmode="numeric" placeholder="000.000.000-00" />
    @endif

    <x-input name="email" label="E-mail" type="email" :value="$p?->email" obrigatorio maxlength="120" autocomplete="email" />
    <x-input name="data_nascimento" label="Data de nascimento" type="date" :value="$p?->data_nascimento?->format('Y-m-d')" obrigatorio max="{{ now()->subDay()->format('Y-m-d') }}" />
    <x-input name="pais" label="País" :value="$p?->pais ?? 'Brasil'" obrigatorio maxlength="60" />
    <x-select name="tipo_participante" label="Tipo de participante" :opcoes="\App\Enums\TipoParticipante::opcoes()" :selecionado="$p?->tipo_participante" obrigatorio />
    <x-input name="instituicao_origem" label="Instituição de origem" :value="$p?->instituicao_origem" obrigatorio maxlength="150"
        list="lista-instituicoes" placeholder="Ex.: Universidade Federal da Grande Dourados" />
    <datalist id="lista-instituicoes">
        @foreach (\App\Models\Participante::query()->distinct()->orderBy('instituicao_origem')->pluck('instituicao_origem') as $inst)
            <option value="{{ $inst }}">
        @endforeach
    </datalist>

    @if ($admin)
        <x-select name="status_conta" label="Status da conta" :opcoes="\App\Enums\StatusConta::opcoes()" :selecionado="$p?->status_conta ?? 'ativo'" :vazio="false" obrigatorio
            ajuda="Contas inativas ou bloqueadas não conseguem entrar no portal." />
    @endif

    <x-lista-dinamica name="telefones" label="Telefones" :itens="$p ? $p->telefones->pluck('telefone')->all() : []"
        tipo="tel" mascara="telefone" placeholder="(67) 99999-9999" textoAdicionar="Adicionar telefone" class="sm:col-span-2"
        ajuda="Com DDD. Números estrangeiros: digite só os números com o código do país." />

    <div class="grid gap-4 sm:col-span-2 sm:grid-cols-2">
        <x-input name="senha" :label="$editando ? 'Nova senha' : 'Senha'" type="password" :obrigatorio="! $editando" autocomplete="new-password"
            :ajuda="$editando ? 'Deixe em branco para manter a senha atual.' : 'Mínimo de 6 caracteres.'" />
        <x-input name="senha_confirmation" label="Confirme a senha" type="password" :obrigatorio="! $editando" autocomplete="new-password" />
    </div>
</div>
