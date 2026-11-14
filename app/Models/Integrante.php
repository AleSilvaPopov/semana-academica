<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Tabela integrante (comissão organizadora). Usuário do guard "integrante" (login por RGA).
 *
 * PK de texto (rga): $keyType string e sem incremento. Na edição o RGA fica somente
 * leitura, porque as FKs que apontam para ele não têm ON UPDATE CASCADE.
 *
 * RELAÇÃO CIRCULAR com coordenadoria:
 *  - integrante.id_coordenadoria (NULL permitido) = coordenadoria em que a pessoa ATUA   -> coordenadoria()
 *  - coordenadoria.rga_coordenador                = coordenadorias que a pessoa COORDENA -> coordenadoriasQueCoordena()
 */
class Integrante extends Authenticatable
{
    protected $table = 'integrante';

    protected $primaryKey = 'rga';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['rga', 'cpf', 'nome_completo', 'id_curso', 'funcao', 'data_ingresso', 'observacoes', 'id_coordenadoria'];

    protected $hidden = ['senha_hash'];

    protected function casts(): array
    {
        return [
            'data_ingresso' => 'date',
            'id_curso' => 'integer',
            'id_coordenadoria' => 'integer',
        ];
    }

    /** A senha fica na coluna senha_hash (SHA-256 hex), não em "password". */
    public function getAuthPasswordName(): string
    {
        return 'senha_hash';
    }

    /** O banco não tem coluna remember_token: desliga o "lembrar de mim". */
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    /** Coordenadoria em que o integrante ATUA (integrante.id_coordenadoria). */
    public function coordenadoria(): BelongsTo
    {
        return $this->belongsTo(Coordenadoria::class, 'id_coordenadoria', 'id_coordenadoria');
    }

    /** Coordenadorias que o integrante COORDENA (coordenadoria.rga_coordenador). */
    public function coordenadoriasQueCoordena(): HasMany
    {
        return $this->hasMany(Coordenadoria::class, 'rga_coordenador', 'rga');
    }

    public function telefones(): HasMany
    {
        return $this->hasMany(IntegranteTelefone::class, 'rga', 'rga');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(IntegranteEmail::class, 'rga', 'rga');
    }

    public function tarefasCriadas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'rga_criador', 'rga');
    }

    /** Tarefas em que é responsável (N:N tarefa_responsavel). */
    public function tarefas(): BelongsToMany
    {
        return $this->belongsToMany(Tarefa::class, 'tarefa_responsavel', 'rga', 'id_tarefa', 'rga', 'id_tarefa');
    }

    public function presidencias(): HasMany
    {
        return $this->hasMany(Presidencia::class, 'rga_presidente', 'rga');
    }

    public function vicePresidencias(): HasMany
    {
        return $this->hasMany(Presidencia::class, 'rga_vice', 'rga');
    }

    /** Ex.: "Mariana Albuquerque Rocha (2023104791)" — usado nos <select>. */
    public function getRotuloAttribute(): string
    {
        return $this->nome_completo.' ('.$this->rga.')';
    }
}
