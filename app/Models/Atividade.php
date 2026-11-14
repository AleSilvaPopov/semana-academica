<?php

namespace App\Models;

use App\Enums\StatusAtividade;
use App\Enums\TipoAtividade;
use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Tabela atividade. NÃO tem id_comissao (3FN): a edição é obtida por
 * atividade -> coordenadoria -> comissao_organizadora (relação comissao() e escopo daEdicao()).
 */
class Atividade extends Model
{
    use GeraProximoId;

    protected $table = 'atividade';

    protected $primaryKey = 'id_atividade';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'nome', 'descricao', 'tipo', 'duracao_minutos', 'data_realizacao', 'vagas',
        'valor_inscricao', 'status', 'id_local', 'id_coordenadoria',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoAtividade::class,
            'status' => StatusAtividade::class,
            'data_realizacao' => 'datetime',
            'duracao_minutos' => 'integer',
            'vagas' => 'integer',
            'valor_inscricao' => 'decimal:2',
            'id_local' => 'integer',
            'id_coordenadoria' => 'integer',
        ];
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(LocalEvento::class, 'id_local', 'id_local');
    }

    public function coordenadoria(): BelongsTo
    {
        return $this->belongsTo(Coordenadoria::class, 'id_coordenadoria', 'id_coordenadoria');
    }

    /** Edição da atividade, através da coordenadoria. */
    public function comissao(): HasOneThrough
    {
        return $this->hasOneThrough(
            Comissao::class,
            Coordenadoria::class,
            'id_coordenadoria', // coluna de coordenadoria casada com atividade.id_coordenadoria
            'id_comissao',      // coluna de comissao_organizadora
            'id_coordenadoria', // chave local em atividade
            'id_comissao'       // coluna de coordenadoria que aponta para a comissão
        );
    }

    /** N:N sem atributos (tabela ministra): gerenciado com sync(). */
    public function ministrantes(): BelongsToMany
    {
        return $this->belongsToMany(Ministrante::class, 'ministra', 'id_atividade', 'cpf_ministrante', 'id_atividade', 'cpf');
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'id_atividade', 'id_atividade');
    }

    /** N:N com atributos (tabela inscricao). */
    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(Participante::class, 'inscricao', 'id_atividade', 'cpf', 'id_atividade', 'cpf')
            ->using(Inscricao::class)
            ->withPivot(['data_inscricao', 'presenca']);
    }

    public function scopeDaEdicao(Builder $query, int $idComissao): Builder
    {
        return $query->whereHas('coordenadoria', fn ($q) => $q->where('id_comissao', $idComissao));
    }

    /** Total de inscritos (usa inscricoes_count quando carregado com withCount). */
    public function getTotalInscritosAttribute(): int
    {
        return (int) ($this->inscricoes_count ?? $this->inscricoes()->count());
    }

    public function getVagasRestantesAttribute(): int
    {
        return max(0, $this->vagas - $this->total_inscritos);
    }

    /** Ainda aceita inscrição: status planejada/confirmada e a data ainda não chegou. */
    public function getInscricoesAbertasAttribute(): bool
    {
        return $this->status->aceitaInscricao() && $this->data_realizacao->isFuture();
    }
}
