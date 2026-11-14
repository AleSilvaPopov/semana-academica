<?php

namespace App\Models;

use App\Models\Concerns\GeraProximoId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tabela curso (criada na normalização: RGA -> curso -> faculdade). */
class Curso extends Model
{
    use GeraProximoId;

    protected $table = 'curso';

    protected $primaryKey = 'id_curso';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['nome_curso', 'faculdade'];

    public function integrantes(): HasMany
    {
        return $this->hasMany(Integrante::class, 'id_curso', 'id_curso');
    }
}
