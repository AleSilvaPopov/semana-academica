<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Tabela inscricao: N:N COM ATRIBUTOS entre participante e atividade.
 * PK composta (cpf, id_atividade) — impede inscrição duplicada.
 * presenca = 1 dá direito ao certificado. Update/delete via Query Builder (escopo daChave()).
 */
class Inscricao extends Pivot
{
    protected $table = 'inscricao';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['cpf', 'id_atividade', 'data_inscricao', 'presenca'];

    protected function casts(): array
    {
        return [
            'id_atividade' => 'integer',
            'data_inscricao' => 'datetime',
            'presenca' => 'boolean',
        ];
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class, 'cpf', 'cpf');
    }

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(Atividade::class, 'id_atividade', 'id_atividade');
    }

    public function scopeDaChave(Builder $query, string $cpf, int $idAtividade): Builder
    {
        return $query->where('cpf', $cpf)->where('id_atividade', $idAtividade);
    }
}
