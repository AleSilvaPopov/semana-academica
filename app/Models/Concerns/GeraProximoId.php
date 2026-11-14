<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Geração de IDs para as tabelas cujas PKs inteiras NÃO são AUTO_INCREMENT
 * (comissao_organizadora, curso, coordenadoria, tarefa, local_evento, atividade).
 *
 * O próximo ID é MAX(id) + 1, lido com SELECT ... FOR UPDATE (lockForUpdate) dentro
 * de uma transação: duas gravações simultâneas não conseguem pegar o mesmo número,
 * porque a segunda espera a primeira terminar.
 */
trait GeraProximoId
{
    /**
     * Próximo ID livre. DEVE ser chamado dentro de DB::transaction() para o lock valer.
     */
    public static function proximoId(): int
    {
        $modelo = new static;

        $maior = DB::table($modelo->getTable())
            ->lockForUpdate()
            ->max($modelo->getKeyName());

        return ((int) $maior) + 1;
    }

    /**
     * Cria o registro já com o novo ID. Se for chamado dentro de outra transação,
     * o Laravel usa um savepoint e o lock dura até a transação externa terminar.
     */
    public static function criarComNovoId(array $atributos): static
    {
        return DB::transaction(function () use ($atributos) {
            $modelo = new static;
            $modelo->fill($atributos);
            $modelo->{$modelo->getKeyName()} = static::proximoId();
            $modelo->save();

            return $modelo;
        });
    }
}
