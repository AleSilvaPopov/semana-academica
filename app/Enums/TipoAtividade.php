<?php

namespace App\Enums;

/** atividade.tipo ENUM('palestra','minicurso','workshop') */
enum TipoAtividade: string
{
    use RotulosEnum;

    case Palestra = 'palestra';
    case Minicurso = 'minicurso';
    case Workshop = 'workshop';

    public function rotulo(): string
    {
        return match ($this) {
            self::Palestra => 'Palestra',
            self::Minicurso => 'Minicurso',
            self::Workshop => 'Workshop',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Palestra => 'blue',
            self::Minicurso => 'purple',
            self::Workshop => 'pink',
        };
    }
}
