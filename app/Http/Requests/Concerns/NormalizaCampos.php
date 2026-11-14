<?php

namespace App\Http\Requests\Concerns;

/**
 * Remove máscaras antes da validação: CPF e telefones são gravados só com dígitos
 * (cpf CHAR(11), telefone VARCHAR(15)). Também apara espaços das listas dinâmicas.
 */
trait NormalizaCampos
{
    protected function normalizarCpf(string $campo = 'cpf'): void
    {
        if ($this->has($campo)) {
            $this->merge([$campo => somente_digitos($this->input($campo))]);
        }
    }

    protected function normalizarLista(string $campo, bool $somenteDigitos = false): void
    {
        $itens = collect((array) $this->input($campo, []))
            ->map(fn ($v) => $somenteDigitos ? somente_digitos((string) $v) : trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();

        $this->merge([$campo => $itens]);
    }

    /** Campos opcionais enviados vazios viram null (ex.: data de término, observações). */
    protected function vazioParaNulo(array $campos): void
    {
        foreach ($campos as $campo) {
            if ($this->has($campo) && trim((string) $this->input($campo)) === '') {
                $this->merge([$campo => null]);
            }
        }
    }
}
