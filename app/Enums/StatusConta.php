<?php

namespace App\Enums;

/** participante.status_conta ENUM('ativo','inativo','bloqueado') */
enum StatusConta: string
{
    use RotulosEnum;

    case Ativo = 'ativo';
    case Inativo = 'inativo';
    case Bloqueado = 'bloqueado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Ativo => 'Ativo',
            self::Inativo => 'Inativo',
            self::Bloqueado => 'Bloqueado',
        };
    }

    /** Concordância com "conta" (feminino): "Sua conta está bloqueada". */
    public function daConta(): string
    {
        return match ($this) {
            self::Ativo => 'ativa',
            self::Inativo => 'inativa',
            self::Bloqueado => 'bloqueada',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Ativo => 'green',
            self::Inativo => 'zinc',
            self::Bloqueado => 'red',
        };
    }
}
