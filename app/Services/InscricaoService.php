<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Atividade;
use App\Models\Inscricao;
use App\Models\Participante;
use Illuminate\Support\Facades\DB;

/**
 * Regras de negócio da inscrição (tabela inscricao, N:N com atributos, PK cpf + id_atividade):
 *  1. só atividades com status 'planejada' ou 'confirmada';
 *  2. a inscrição deve ser anterior à data da atividade;
 *  3. sem inscrição duplicada;
 *  4. inscritos não podem passar das vagas;
 *  5. conta do participante precisa estar ativa.
 * Cancelamento: só antes da data da atividade e sem presença registrada.
 * Presença (presenca = 1) dá direito ao certificado.
 */
class InscricaoService
{
    public function inscrever(Participante $participante, Atividade $atividade): void
    {
        DB::transaction(function () use ($participante, $atividade) {
            // Trava a linha da atividade: duas inscrições simultâneas na última vaga
            // são processadas uma de cada vez (a segunda já vê a atividade lotada).
            $atividade = Atividade::whereKey($atividade->id_atividade)->lockForUpdate()->firstOrFail();

            if (! $participante->podeEntrar()) {
                throw new RegraNegocioException('A conta deste participante está '.$participante->status_conta->daConta().' e não pode se inscrever.');
            }

            if (! $atividade->status->aceitaInscricao()) {
                throw new RegraNegocioException('Inscrições só são aceitas em atividades planejadas ou confirmadas (status atual: '.$atividade->status->rotulo().').');
            }

            if (! $atividade->data_realizacao->isFuture()) {
                throw new RegraNegocioException('As inscrições para esta atividade foram encerradas: ela já aconteceu ou está acontecendo.');
            }

            if (Inscricao::daChave($participante->cpf, $atividade->id_atividade)->exists()) {
                throw new RegraNegocioException('Este participante já está inscrito nesta atividade.');
            }

            $inscritos = Inscricao::where('id_atividade', $atividade->id_atividade)->count();
            if ($inscritos >= $atividade->vagas) {
                throw new RegraNegocioException('Atividade lotada: todas as '.$atividade->vagas.' vagas já foram preenchidas.');
            }

            // attach() grava a linha N:N com os atributos do relacionamento.
            $atividade->participantes()->attach($participante->cpf, [
                'data_inscricao' => now(),
                'presenca' => false,
            ]);
        });
    }

    public function cancelar(Participante $participante, Atividade $atividade): void
    {
        $inscricao = Inscricao::daChave($participante->cpf, $atividade->id_atividade)->first();

        if (! $inscricao) {
            throw new RegraNegocioException('Inscrição não encontrada.');
        }

        if (! $atividade->data_realizacao->isFuture()) {
            throw new RegraNegocioException('Não é possível cancelar: a atividade já aconteceu.');
        }

        if ($inscricao->presenca) {
            throw new RegraNegocioException('Não é possível cancelar uma inscrição com presença registrada.');
        }

        $this->apagar($participante->cpf, $atividade->id_atividade);
    }

    /** Remoção pelo painel: permitida a qualquer momento, desde que não haja presença (certificado). */
    public function remover(string $cpf, Atividade $atividade): void
    {
        $inscricao = Inscricao::daChave($cpf, $atividade->id_atividade)->firstOrFail();

        if ($inscricao->presenca) {
            throw new RegraNegocioException('Esta inscrição tem presença registrada (dá direito a certificado). Desmarque a presença antes de remover.');
        }

        $this->apagar($cpf, $atividade->id_atividade);
    }

    /**
     * Credenciamento em lote: marca presença para os CPFs enviados e desmarca os demais.
     *
     * @param  array<int, string>  $cpfsPresentes
     * @return int quantidade de presentes
     */
    public function registrarPresencas(Atividade $atividade, array $cpfsPresentes): int
    {
        if ($atividade->data_realizacao->isAfter(today()->endOfDay())) {
            throw new RegraNegocioException('A presença só pode ser registrada a partir do dia da atividade ('.formata_data($atividade->data_realizacao).').');
        }

        return DB::transaction(function () use ($atividade, $cpfsPresentes) {
            $base = fn () => DB::table('inscricao')->where('id_atividade', $atividade->id_atividade);

            $base()->update(['presenca' => false]);

            if ($cpfsPresentes !== []) {
                $base()->whereIn('cpf', $cpfsPresentes)->update(['presenca' => true]);
            }

            return $base()->where('presenca', true)->count();
        });
    }

    /** DELETE ... WHERE cpf = ? AND id_atividade = ?  (PK composta, via Query Builder). */
    private function apagar(string $cpf, int $idAtividade): void
    {
        DB::table('inscricao')->where('cpf', $cpf)->where('id_atividade', $idAtividade)->delete();
    }
}
