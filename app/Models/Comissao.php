<?php

namespace App\Models;

use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Tabela comissao_organizadora: uma linha por EDIÇÃO do evento.
 * PK id_comissao sem AUTO_INCREMENT (ver GeraProximoId).
 */
class Comissao extends Model
{
    use GeraProximoId;

    protected $table = 'comissao_organizadora';

    protected $primaryKey = 'id_comissao';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['nome_evento', 'edicao', 'data_inicio', 'data_termino', 'local_sede', 'observacoes'];

    protected function casts(): array
    {
        return [
            'edicao' => 'integer',
            'data_inicio' => 'date',
            'data_termino' => 'date',
        ];
    }

    /** Presidente e vice da edição (1 linha por edição na tabela presidencia). */
    public function presidencia(): HasOne
    {
        return $this->hasOne(Presidencia::class, 'id_comissao', 'id_comissao');
    }

    public function coordenadorias(): HasMany
    {
        return $this->hasMany(Coordenadoria::class, 'id_comissao', 'id_comissao');
    }

    /** Atividade não tem id_comissao: a edição vem pela coordenadoria que a organiza. */
    public function atividades(): HasManyThrough
    {
        return $this->hasManyThrough(
            Atividade::class,
            Coordenadoria::class,
            'id_comissao',       // FK em coordenadoria
            'id_coordenadoria',  // FK em atividade
            'id_comissao',       // PK local
            'id_coordenadoria'   // PK de coordenadoria
        );
    }

    public function patrocinios(): HasMany
    {
        return $this->hasMany(Patrocinio::class, 'id_comissao', 'id_comissao');
    }

    /** N:N com atributos (tabela patrocinio). */
    public function patrocinadores(): BelongsToMany
    {
        return $this->belongsToMany(Patrocinador::class, 'patrocinio', 'id_comissao', 'nome_patrocinador', 'id_comissao', 'nome')
            ->using(Patrocinio::class)
            ->withPivot(['tipo_patrocinio', 'descricao_patrocinio', 'valor', 'requisitos']);
    }

    /** Ordena da edição mais recente para a mais antiga. */
    public function scopeRecentesPrimeiro(Builder $query): Builder
    {
        return $query->orderByDesc('data_inicio')->orderByDesc('edicao');
    }

    public static function maisRecente(): ?self
    {
        return static::query()->recentesPrimeiro()->first();
    }

    /** Ex.: "10ª edição (2026)" */
    public function getTituloAttribute(): string
    {
        return $this->edicao.'ª edição ('.$this->data_inicio?->year.')';
    }

    /** Ex.: "Semana Acadêmica de Computação da UFGD — 10ª edição" */
    public function getNomeCompletoAttribute(): string
    {
        return $this->nome_evento.' — '.$this->edicao.'ª edição';
    }

    public function getPeriodoAttribute(): string
    {
        return formata_data($this->data_inicio).' a '.formata_data($this->data_termino);
    }
}
