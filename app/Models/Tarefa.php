<?php

namespace App\Models;

use App\Enums\StatusTarefa;
use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Tabela tarefa. Só coordenadores podem criar (triggers trg_tarefa_criador_bi/bu).
 *
 * A tabela NÃO guarda edição nem coordenadoria. Convenção do sistema: a tarefa pertence à
 * coordenadoria que o criador coordenava na data_criacao (e, portanto, à edição dela).
 */
class Tarefa extends Model
{
    use GeraProximoId;

    protected $table = 'tarefa';

    protected $primaryKey = 'id_tarefa';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['descricao', 'data_criacao', 'prazo_final', 'status', 'rga_criador'];

    protected function casts(): array
    {
        return [
            'data_criacao' => 'date',
            'prazo_final' => 'date',
            'status' => StatusTarefa::class,
        ];
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(Integrante::class, 'rga_criador', 'rga');
    }

    /** N:N sem atributos (tarefa_responsavel): gerenciado com sync(). */
    public function responsaveis(): BelongsToMany
    {
        return $this->belongsToMany(Integrante::class, 'tarefa_responsavel', 'id_tarefa', 'rga', 'id_tarefa', 'rga');
    }

    /** Subconsulta: coordenação do criador que estava vigente na data de criação da tarefa. */
    private static function coordenacaoVigente(QueryBuilder $sub): QueryBuilder
    {
        return $sub->selectRaw('1')
            ->from('coordenadoria as cv')
            ->whereColumn('cv.rga_coordenador', 'tarefa.rga_criador')
            ->whereColumn('tarefa.data_criacao', '>=', 'cv.data_inicio_coordenacao')
            ->where(fn ($q) => $q->whereNull('cv.data_termino_coordenacao')
                ->orWhereColumn('tarefa.data_criacao', '<=', 'cv.data_termino_coordenacao'));
    }

    public function scopeDaEdicao(Builder $query, int $idComissao): Builder
    {
        return $query->whereExists(fn ($sub) => self::coordenacaoVigente($sub)->where('cv.id_comissao', $idComissao));
    }

    public function scopeDaCoordenadoria(Builder $query, Coordenadoria $coordenadoria): Builder
    {
        return $query->whereExists(fn ($sub) => self::coordenacaoVigente($sub)
            ->where('cv.id_coordenadoria', $coordenadoria->id_coordenadoria));
    }

    /** Pendentes ou em andamento com prazo vencido. Usa o índice idx_tarefa_status_prazo. */
    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query->whereIn('tarefa.status', StatusTarefa::abertos())
            ->where('tarefa.prazo_final', '<', today()->toDateString());
    }

    public function getAtrasadaAttribute(): bool
    {
        return in_array($this->status?->value, StatusTarefa::abertos(), true)
            && (bool) $this->prazo_final?->lt(today());
    }

    /** Coordenadoria à qual a tarefa pertence (pela convenção acima). */
    public function coordenadoriaVigente(): ?Coordenadoria
    {
        return Coordenadoria::query()
            ->where('rga_coordenador', $this->rga_criador)
            ->whereDate('data_inicio_coordenacao', '<=', $this->data_criacao)
            ->where(fn ($q) => $q->whereNull('data_termino_coordenacao')
                ->orWhereDate('data_termino_coordenacao', '>=', $this->data_criacao))
            ->with('comissao')
            ->first();
    }
}
