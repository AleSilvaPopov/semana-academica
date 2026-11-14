<?php

namespace App\Enums;

/** participante.tipo_participante ENUM('discente','docente','externo') */
enum TipoParticipante: string
{
    use RotulosEnum;

    case Discente = 'discente';
    case Docente = 'docente';
    case Externo = 'externo';

    public function rotulo(): string
    {
        return match ($this) {
            self::Discente => 'Discente',
            self::Docente => 'Docente',
            self::Externo => 'Externo',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Discente => 'blue',
            self::Docente => 'purple',
            self::Externo => 'amber',
        };
    }
}
