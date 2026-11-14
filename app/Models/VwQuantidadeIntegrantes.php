<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Model SOMENTE LEITURA da view vw_quantidade_integrantes
 * (atributo derivado "Quantidade Integrantes" da coordenadoria).
 */
class VwQuantidadeIntegrantes extends Model
{
    protected $table = 'vw_quantidade_integrantes';

    protected $primaryKey = 'id_coordenadoria';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['quantidade_integrantes' => 'integer', 'edicao' => 'integer'];
    }

    public function save(array $options = []): bool
    {
        throw new LogicException('vw_quantidade_integrantes é uma view: somente leitura.');
    }

    public function delete(): ?bool
    {
        throw new LogicException('vw_quantidade_integrantes é uma view: somente leitura.');
    }
}
