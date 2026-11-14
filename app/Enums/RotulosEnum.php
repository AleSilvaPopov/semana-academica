<?php

namespace App\Enums;

/**
 * Comportamento comum dos PHP Enums que espelham os ENUMs do MySQL:
 * rótulo em português, cor do badge e lista de opções para os <select>.
 */
trait RotulosEnum
{
    abstract public function rotulo(): string;

    abstract public function cor(): string;

    /** @return array<string, string> [valor no banco => rótulo] */
    public static function opcoes(): array
    {
        $opcoes = [];

        foreach (self::cases() as $caso) {
            $opcoes[$caso->value] = $caso->rotulo();
        }

        return $opcoes;
    }

    /** @return array<int, string> valores aceitos (para a regra "in" da validação) */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
