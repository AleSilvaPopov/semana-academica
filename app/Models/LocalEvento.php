<?php

namespace App\Models;

use App\Enums\TipoLocal;
use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabela local_evento ("local" é palavra reservada no MySQL).
 * O atributo composto Localização virou faculdade + bloco + numero (UNIQUE).
 */
class LocalEvento extends Model
{
    use GeraProximoId;

    protected $table = 'local_evento';

    protected $primaryKey = 'id_local';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['tipo', 'faculdade', 'bloco', 'numero', 'capacidade', 'observacoes'];

    protected function casts(): array
    {
        return [
            'tipo' => TipoLocal::class,
            'capacidade' => 'integer',
        ];
    }

    public function equipamentos(): HasMany
    {
        return $this->hasMany(LocalEquipamento::class, 'id_local', 'id_local');
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class, 'id_local', 'id_local');
    }

    /** Ex.: "FACET · AUD-01" */
    public function getNomeCurtoAttribute(): string
    {
        return $this->bloco.' · '.$this->numero;
    }

    /** Ex.: "FACET · AUD-01 — Auditório (220 lugares)" */
    public function getRotuloAttribute(): string
    {
        return $this->nome_curto.' — '.$this->tipo?->rotulo().' ('.$this->capacidade.' lugares)';
    }
}
