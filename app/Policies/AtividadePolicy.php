<?php

namespace App\Policies;

use App\Models\Atividade;
use App\Models\Integrante;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;

/**
 * Atividades: presidência da edição gerencia todas; o coordenador gerencia as da própria
 * coordenadoria (inclusive inscrições e presença).
 */
class AtividadePolicy
{
    public function __construct(private Permissoes $permissoes, private EdicaoAtual $edicao)
    {
    }

    public function create(Integrante $integrante): bool
    {
        $id = $this->edicao->id();

        return $this->permissoes->presideEdicao($integrante, $id)
            || $this->permissoes->coordenaNaEdicao($integrante, $id);
    }

    public function update(Integrante $integrante, Atividade $atividade): bool
    {
        $atividade->loadMissing('coordenadoria');

        return $atividade->coordenadoria
            && $this->permissoes->gerenciaCoordenadoria($integrante, $atividade->coordenadoria);
    }

    public function delete(Integrante $integrante, Atividade $atividade): bool
    {
        return $this->update($integrante, $atividade);
    }

    /** Registrar presença e incluir/remover inscrições manualmente. */
    public function gerenciarInscricoes(Integrante $integrante, Atividade $atividade): bool
    {
        return $this->update($integrante, $atividade);
    }
}
