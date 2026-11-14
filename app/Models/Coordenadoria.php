<?php

namespace App\Models;

use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Tabela coordenadoria. Pertence a uma edição (id_comissao) e tem um coordenador
 * (rga_coordenador), com as datas da coordenação. Ver a relação circular em Integrante.
 */
class Coordenadoria extends Model
{
    use GeraProximoId;

    protected $table = 'coordenadoria';

    protected $primaryKey = 'id_coordenadoria';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'nome', 'descricao_atuacao', 'id_comissao', 'rga_coordenador',
        'data_inicio_coordenacao', 'data_termino_coordenacao',
    ];

    protected function casts(): array
    {
        return [
            'id_comissao' => 'integer',
            'data_inicio_coordenacao' => 'date',
            'data_termino_coordenacao' => 'date',
        ];
    }

    public function comissao(): BelongsTo
    {
        return $this->belongsTo(Comissao::class, 'id_comissao', 'id_comissao');
    }

    /** Quem coordena (coordenadoria.rga_coordenador). */
    public function coordenador(): BelongsTo
    {
        return $this->belongsTo(Integrante::class, 'rga_coordenador', 'rga');
    }

    /** Quem atua nela (integrante.id_coordenadoria). */
    public function membros(): HasMany
    {
        return $this->hasMany(Integrante::class, 'id_coordenadoria', 'id_coordenadoria');
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class, 'id_coordenadoria', 'id_coordenadoria');
    }

    /** Atributo derivado "Quantidade Integrantes", vindo da view vw_quantidade_integrantes. */
    public function quantidade(): HasOne
    {
        return $this->hasOne(VwQuantidadeIntegrantes::class, 'id_coordenadoria', 'id_coordenadoria');
    }

    public function scopeDaEdicao(Builder $query, int $idComissao): Builder
    {
        return $query->where('coordenadoria.id_comissao', $idComissao);
    }

    /** Tarefas criadas pelo coordenador durante esta coordenação (ver Tarefa::scopeDaCoordenadoria). */
    public function tarefas(): Builder
    {
        return Tarefa::query()->daCoordenadoria($this);
    }

    /** Ex.: "Infraestrutura — 10ª edição (2026)" */
    public function getRotuloAttribute(): string
    {
        return $this->nome.($this->relationLoaded('comissao') && $this->comissao ? ' — '.$this->comissao->titulo : '');
    }
}
