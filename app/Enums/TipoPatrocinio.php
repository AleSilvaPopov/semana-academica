<?php

namespace App\Enums;

/** patrocinio.tipo_patrocinio ENUM('financeiro','equipamento','servico','material') */
enum TipoPatrocinio: string
{
    use RotulosEnum;

    case Financeiro = 'financeiro';
    case Equipamento = 'equipamento';
    case Servico = 'servico';
    case Material = 'material';

    public function rotulo(): string
    {
        return match ($this) {
            self::Financeiro => 'Financeiro',
            self::Equipamento => 'Equipamento',
            self::Servico => 'Serviço',
            self::Material => 'Material',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Financeiro => 'green',
            self::Equipamento => 'purple',
            self::Servico => 'sky',
            self::Material => 'amber',
        };
    }
}
