<?php

namespace App\Services;

use App\Models\Comissao;
use App\Models\Presidencia;
use Illuminate\Support\Facades\DB;

/**
 * Edição + presidência. A tabela presidencia tem PK = id_comissao: exatamente uma linha
 * por edição, com presidente e vice diferentes. Os dois são gravados na mesma transação
 * da edição, para nunca existir edição sem presidência.
 */
class PresidenciaService
{
    public function criarEdicao(array $dados): Comissao
    {
        return DB::transaction(function () use ($dados) {
            $comissao = Comissao::criarComNovoId(collect($dados)->except(['rga_presidente', 'rga_vice'])->all());
            $this->definir($comissao, $dados['rga_presidente'], $dados['rga_vice']);

            return $comissao;
        });
    }

    public function atualizarEdicao(Comissao $comissao, array $dados): Comissao
    {
        return DB::transaction(function () use ($comissao, $dados) {
            $comissao->update(collect($dados)->except(['rga_presidente', 'rga_vice'])->all());
            $this->definir($comissao, $dados['rga_presidente'], $dados['rga_vice']);

            return $comissao;
        });
    }

    /** Cria ou substitui a única linha de presidência da edição. */
    public function definir(Comissao $comissao, string $rgaPresidente, string $rgaVice): Presidencia
    {
        return Presidencia::updateOrCreate(
            ['id_comissao' => $comissao->id_comissao],
            ['rga_presidente' => $rgaPresidente, 'rga_vice' => $rgaVice]
        );
    }

    /**
     * Exclui a edição. A presidência sai junto (é parte da edição); se houver coordenadorias
     * ou patrocínios, o MySQL recusa (erro 1451), a transação é desfeita e o ErrosBanco
     * mostra a mensagem amigável.
     */
    public function excluirEdicao(Comissao $comissao): void
    {
        DB::transaction(function () use ($comissao) {
            Presidencia::where('id_comissao', $comissao->id_comissao)->delete();
            $comissao->delete();
        });
    }
}
