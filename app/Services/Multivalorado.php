<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Grava atributos multivalorados (telefones, e-mails, certificados, equipamentos).
 *
 * Essas tabelas têm PK composta (dono + valor) e não têm CRUD próprio: o formulário da
 * entidade dona envia a lista inteira e aqui as linhas antigas são APAGADAS e as novas
 * REINSERIDAS, tudo dentro de uma transação.
 */
class Multivalorado
{
    /**
     * @param  HasMany  $relacao  ex.: $integrante->telefones()
     * @param  string  $coluna  coluna do valor, ex.: 'telefone'
     * @param  array<int, string|null>  $valores  valores já validados/normalizados
     */
    public static function sincronizar(HasMany $relacao, string $coluna, array $valores): void
    {
        $limpos = collect($valores)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->unique(fn ($v) => mb_strtolower($v)) // a collation _ai_ci trata maiúsculas/acentos como iguais
            ->values();

        DB::transaction(function () use ($relacao, $coluna, $limpos) {
            $relacao->delete();

            if ($limpos->isNotEmpty()) {
                $relacao->createMany($limpos->map(fn ($v) => [$coluna => $v])->all());
            }
        });
    }
}
