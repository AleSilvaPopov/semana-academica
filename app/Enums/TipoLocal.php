<?php

namespace App\Enums;

/** local_evento.tipo ENUM('auditorio','laboratorio','sala') */
enum TipoLocal: string
{
    use RotulosEnum;

    case Auditorio = 'auditorio';
    case Laboratorio = 'laboratorio';
    case Sala = 'sala';

    public function rotulo(): string
    {
        return match ($this) {
            self::Auditorio => 'Auditório',
            self::Laboratorio => 'Laboratório',
            self::Sala => 'Sala',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Auditorio => 'purple',
            self::Laboratorio => 'sky',
            self::Sala => 'zinc',
        };
    }
}
