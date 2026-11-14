<?php

namespace App\Enums;

/** atividade.status ENUM('planejada','confirmada','cancelada','realizada') */
enum StatusAtividade: string
{
    use RotulosEnum;

    case Planejada = 'planejada';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';
    case Realizada = 'realizada';

    public function rotulo(): string
    {
        return match ($this) {
            self::Planejada => 'Planejada',
            self::Confirmada => 'Confirmada',
            self::Cancelada => 'Cancelada',
            self::Realizada => 'Realizada',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Planejada => 'amber',
            self::Confirmada => 'blue',
            self::Cancelada => 'red',
            self::Realizada => 'green',
        };
    }

    /** Regra de negócio: só aceita inscrição atividade planejada ou confirmada. */
    public function aceitaInscricao(): bool
    {
        return in_array($this, [self::Planejada, self::Confirmada], true);
    }
}
