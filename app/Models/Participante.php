<?php

namespace App\Models;

use App\Enums\StatusConta;
use App\Enums\TipoParticipante;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Tabela participante (público do evento). Usuário do guard "participante" (login por e-mail).
 * PK de texto (cpf CHAR(11), só dígitos), somente leitura na edição.
 */
class Participante extends Authenticatable
{
    protected $table = 'participante';

    protected $primaryKey = 'cpf';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'cpf', 'nome_completo', 'email', 'pais', 'data_nascimento',
        'status_conta', 'tipo_participante', 'instituicao_origem',
    ];

    protected $hidden = ['senha_hash'];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'status_conta' => StatusConta::class,
            'tipo_participante' => TipoParticipante::class,
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'senha_hash';
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    /** Regra de negócio: conta 'inativo' ou 'bloqueado' não faz login. */
    public function podeEntrar(): bool
    {
        return $this->status_conta === StatusConta::Ativo;
    }

    public function telefones(): HasMany
    {
        return $this->hasMany(ParticipanteTelefone::class, 'cpf', 'cpf');
    }

    /** Especialização: o participante pode ser também ministrante (mesmo cpf). */
    public function ministrante(): HasOne
    {
        return $this->hasOne(Ministrante::class, 'cpf', 'cpf');
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'cpf', 'cpf');
    }

    /** N:N com atributos (tabela inscricao). */
    public function atividades(): BelongsToMany
    {
        return $this->belongsToMany(Atividade::class, 'inscricao', 'cpf', 'id_atividade', 'cpf', 'id_atividade')
            ->using(Inscricao::class)
            ->withPivot(['data_inscricao', 'presenca']);
    }

    public function getRotuloAttribute(): string
    {
        return $this->nome_completo.' ('.formata_cpf($this->cpf).')';
    }
}
