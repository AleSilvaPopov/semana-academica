<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tabela patrocinador. PK de texto: nome (somente leitura na edição). */
class Patrocinador extends Model
{
    protected $table = 'patrocinador';

    protected $primaryKey = 'nome';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['nome', 'observacoes'];

    public function patrocinios(): HasMany
    {
        return $this->hasMany(Patrocinio::class, 'nome_patrocinador', 'nome');
    }

    public function comissoes(): BelongsToMany
    {
        return $this->belongsToMany(Comissao::class, 'patrocinio', 'nome_patrocinador', 'id_comissao', 'nome', 'id_comissao')
            ->using(Patrocinio::class)
            ->withPivot(['tipo_patrocinio', 'descricao_patrocinio', 'valor', 'requisitos']);
    }
}
