<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tabela presidencia (especialização Presidente/Vice de integrante).
 * PK = FK id_comissao: exatamente uma linha por edição. CHECK rga_presidente <> rga_vice.
 */
class Presidencia extends Model
{
    protected $table = 'presidencia';

    protected $primaryKey = 'id_comissao';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id_comissao', 'rga_presidente', 'rga_vice'];

    public function comissao(): BelongsTo
    {
        return $this->belongsTo(Comissao::class, 'id_comissao', 'id_comissao');
    }

    public function presidente(): BelongsTo
    {
        return $this->belongsTo(Integrante::class, 'rga_presidente', 'rga');
    }

    public function vice(): BelongsTo
    {
        return $this->belongsTo(Integrante::class, 'rga_vice', 'rga');
    }
}
