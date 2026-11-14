<?php

namespace App\Policies;

use App\Models\Integrante;
use App\Models\Tarefa;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;

/**
 * Tarefas: só coordenadores criam (regra também garantida pelo trigger). A presidência da
 * edição pode cadastrar escolhendo um coordenador como criador. O responsável pode apenas
 * alterar o status das tarefas em que foi designado.
 */
class TarefaPolicy
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

    public function update(Integrante $integrante, Tarefa $tarefa): bool
    {
        if ($tarefa->rga_criador === $integrante->rga) {
            return true;
        }

        $idComissao = $tarefa->coordenadoriaVigente()?->id_comissao ?? $this->edicao->id();

        return $this->permissoes->presideEdicao($integrante, $idComissao);
    }

    public function delete(Integrante $integrante, Tarefa $tarefa): bool
    {
        return $this->update($integrante, $tarefa);
    }

    public function alterarStatus(Integrante $integrante, Tarefa $tarefa): bool
    {
        return $this->update($integrante, $tarefa)
            || $tarefa->responsaveis()->where('tarefa_responsavel.rga', $integrante->rga)->exists();
    }
}
