<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida o CPF (11 dígitos + os dois dígitos verificadores).
 * O valor já deve chegar sem máscara (os Form Requests removem pontos e traço antes).
 */
class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::valido((string) $value)) {
            $fail('O CPF informado é inválido.');
        }
    }

    public static function valido(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        // 11 dígitos e não pode ser sequência repetida (000.000.000-00, 111..., etc.)
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula o 10º e o 11º dígitos: soma ponderada (pesos 10..2 e 11..2), resto da divisão por 11.
        for ($posicao = 9; $posicao < 11; $posicao++) {
            $soma = 0;
            for ($i = 0; $i < $posicao; $i++) {
                $soma += (int) $cpf[$i] * ($posicao + 1 - $i);
            }
            $digito = ($soma * 10) % 11 % 10;

            if ((int) $cpf[$posicao] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
