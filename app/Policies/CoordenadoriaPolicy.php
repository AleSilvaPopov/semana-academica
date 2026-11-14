<?php

namespace App\Policies;

use App\Models\Coordenadoria;
use App\Models\Integrante;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;

/** Coordenadorias: só a presidência da edição cria, altera e exclui. */
class CoordenadoriaPolicy
{
    public function __construct(private Permissoes $permissoes, private EdicaoAtual $edicao)
    {
    }

    public function create(Integrante $integrante): bool
    {
        return $this->permissoes->presideEdicao($integrante, $this->edicao->id());
    }

    public function update(Integrante $integrante, Coordenadoria $coordenadoria): bool
    {
        return $this->permissoes->presideEdicao($integrante, $coordenadoria->id_comissao);
    }

    public function delete(Integrante $integrante, Coordenadoria $coordenadoria): bool
    {
        return $this->update($integrante, $coordenadoria);
    }
}
