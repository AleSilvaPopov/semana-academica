<?php

namespace App\Models;

use App\Enums\TipoPatrocinio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Tabela patrocinio: N:N COM ATRIBUTOS entre patrocinador e comissao_organizadora.
 * PK composta (nome_patrocinador, id_comissao). Como o Eloquent não atualiza/apaga por
 * chave composta, update e delete são feitos com Query Builder filtrando as duas colunas
 * (escopo daChave(), usado no Admin\PatrocinioController).
 */
class Patrocinio extends Pivot
{
    protected $table = 'patrocinio';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['nome_patrocinador', 'id_comissao', 'tipo_patrocinio', 'descricao_patrocinio', 'valor', 'requisitos'];

    protected function casts(): array
    {
        return [
            'id_comissao' => 'integer',
            'tipo_patrocinio' => TipoPatrocinio::class,
            'valor' => 'decimal:2',
        ];
    }

    public function patrocinador(): BelongsTo
    {
        return $this->belongsTo(Patrocinador::class, 'nome_patrocinador', 'nome');
    }

    public function comissao(): BelongsTo
    {
        return $this->belongsTo(Comissao::class, 'id_comissao', 'id_comissao');
    }

    /** Filtra pela PK composta. */
    public function scopeDaChave(Builder $query, int $idComissao, string $nomePatrocinador): Builder
    {
        return $query->where('id_comissao', $idComissao)->where('nome_patrocinador', $nomePatrocinador);
    }
}
