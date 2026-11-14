<?php

namespace App\Services;

use App\Models\Coordenadoria;
use App\Models\Integrante;
use App\Models\Presidencia;
use Illuminate\Support\Collection;

/**
 * Papéis do integrante logado em cada edição (seção 6 do enunciado):
 *  - Presidente/Vice (tabela presidencia) -> acesso total à edição
 *  - Coordenador (coordenadoria.rga_coordenador) -> atividades/tarefas/presença da própria coordenadoria
 *  - Integrante comum -> leitura, "Minhas tarefas" e status das tarefas em que é responsável
 *
 * Os dados são lidos uma vez por requisição e reutilizados pelas Policies e Gates.
 */
class Permissoes
{
    /** @var array<string, array{presidencias: Collection, coordenacoes: Collection}> */
    private array $cache = [];

    private function dados(Integrante $integrante): array
    {
        return $this->cache[$integrante->rga] ??= [
            'presidencias' => Presidencia::query()
                ->where('rga_presidente', $integrante->rga)
                ->orWhere('rga_vice', $integrante->rga)
                ->get(['id_comissao', 'rga_presidente', 'rga_vice']),
            'coordenacoes' => Coordenadoria::query()
                ->where('rga_coordenador', $integrante->rga)
                ->get(['id_coordenadoria', 'id_comissao', 'nome']),
        ];
    }

    /** Presidente ou vice da edição. */
    public function presideEdicao(Integrante $integrante, ?int $idComissao): bool
    {
        return $idComissao !== null
            && $this->dados($integrante)['presidencias']->contains('id_comissao', $idComissao);
    }

    /** Presidente ou vice de QUALQUER edição (libera os cadastros globais). */
    public function presideAlgumaEdicao(Integrante $integrante): bool
    {
        return $this->dados($integrante)['presidencias']->isNotEmpty();
    }

    /** IDs das coordenadorias que o integrante coordena na edição. */
    public function coordenadoriasNaEdicao(Integrante $integrante, ?int $idComissao): array
    {
        return $this->dados($integrante)['coordenacoes']
            ->where('id_comissao', $idComissao)
            ->pluck('id_coordenadoria')
            ->all();
    }

    public function coordenaNaEdicao(Integrante $integrante, ?int $idComissao): bool
    {
        return $this->coordenadoriasNaEdicao($integrante, $idComissao) !== [];
    }

    public function coordena(Integrante $integrante, int $idCoordenadoria): bool
    {
        return $this->dados($integrante)['coordenacoes']->contains('id_coordenadoria', $idCoordenadoria);
    }

    /** Pode gerenciar a coordenadoria: presidência da edição dela ou é o coordenador. */
    public function gerenciaCoordenadoria(Integrante $integrante, Coordenadoria $coordenadoria): bool
    {
        return $this->presideEdicao($integrante, $coordenadoria->id_comissao)
            || $this->coordena($integrante, $coordenadoria->id_coordenadoria);
    }

    /** Texto exibido no cabeçalho: papel do usuário na edição selecionada. */
    public function papelNaEdicao(Integrante $integrante, ?int $idComissao): string
    {
        $presidencia = $this->dados($integrante)['presidencias']->firstWhere('id_comissao', $idComissao);

        if ($presidencia) {
            return $presidencia->rga_presidente === $integrante->rga ? 'Presidente da edição' : 'Vice-presidente da edição';
        }

        $coordenacoes = $this->dados($integrante)['coordenacoes']->where('id_comissao', $idComissao);

        if ($coordenacoes->isNotEmpty()) {
            return 'Coordenação: '.$coordenacoes->pluck('nome')->join(', ');
        }

        return 'Integrante';
    }
}
