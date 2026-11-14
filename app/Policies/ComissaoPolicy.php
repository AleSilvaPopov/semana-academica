<?php

namespace App\Policies;

use App\Models\Comissao;
use App\Models\Integrante;
use App\Services\Permissoes;

/** Edições: criar = presidente/vice de qualquer edição; alterar/excluir = presidência da própria edição. */
class ComissaoPolicy
{
    public function __construct(private Permissoes $permissoes)
    {
    }

    public function create(Integrante $integrante): bool
    {
        return $this->permissoes->presideAlgumaEdicao($integrante);
    }

    public function update(Integrante $integrante, Comissao $comissao): bool
    {
        return $this->permissoes->presideEdicao($integrante, $comissao->id_comissao);
    }

    public function delete(Integrante $integrante, Comissao $comissao): bool
    {
        return $this->update($integrante, $comissao);
    }
}
