<?php

namespace App\Services;

use App\Models\Atividade;
use App\Models\Inscricao;
use App\Models\Participante;

/** Certificado de participação: só para inscrição com presenca = 1. */
class CertificadoService
{
    public function temDireito(Participante $participante, Atividade $atividade): bool
    {
        return Inscricao::daChave($participante->cpf, $atividade->id_atividade)->where('presenca', true)->exists();
    }

    public function dados(Participante $participante, Atividade $atividade): array
    {
        abort_unless($this->temDireito($participante, $atividade), 404, 'Certificado disponível apenas para participantes com presença confirmada.');

        $atividade->loadMissing('coordenadoria.comissao', 'ministrantes.participante');

        return [
            'participante' => $participante,
            'atividade' => $atividade,
            'edicao' => $atividade->coordenadoria->comissao,
            'cargaHoraria' => carga_horaria($atividade->duracao_minutos),
            'codigo' => $this->codigo($participante, $atividade),
        ];
    }

    /** Código de verificação derivado da APP_KEY (não é salvo no banco). */
    public function codigo(Participante $participante, Atividade $atividade): string
    {
        $hash = strtoupper(substr(hash_hmac('sha256', $participante->cpf.'|'.$atividade->id_atividade, (string) config('app.key')), 0, 12));

        return implode('-', str_split($hash, 4));
    }
}
