<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Busca por texto e ordenação padronizadas das listagens do painel.
 * A ordenação só aceita colunas da lista branca (evita SQL injection por ?ordem=).
 */
trait Listagem
{
    protected int $porPagina = 15;

    /**
     * @param  array<int, string>  $colunas  colunas pesquisadas com LIKE
     */
    protected function buscar(Builder $query, ?string $termo, array $colunas): Builder
    {
        $termo = trim((string) $termo);

        if ($termo === '') {
            return $query;
        }

        $digitos = somente_digitos($termo);

        return $query->where(function (Builder $q) use ($termo, $colunas, $digitos) {
            foreach ($colunas as $coluna) {
                // CPF e telefone ficam só com dígitos no banco: busca também sem a máscara.
                $valor = in_array($coluna, ['cpf', 'participante.cpf', 'integrante.cpf'], true) && $digitos !== '' ? $digitos : $termo;
                $q->orWhere($coluna, 'like', '%'.$valor.'%');
            }
        });
    }

    /**
     * @param  array<string, string|Expression>  $colunas  [chave da URL => coluna SQL]
     */
    protected function ordenar(Builder $query, Request $request, array $colunas, string $padrao, string $direcaoPadrao = 'asc'): Builder
    {
        $chave = $request->query('ordem');

        if (! is_string($chave) || ! array_key_exists($chave, $colunas)) {
            $chave = $padrao;
            $direcao = $direcaoPadrao;
        } else {
            $direcao = $request->query('direcao') === 'desc' ? 'desc' : 'asc';
        }

        return $query->orderBy($colunas[$chave], $direcao);
    }
}
