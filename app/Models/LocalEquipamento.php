<?php

namespace App\Models;

use App\Models\Concerns\AtributoMultivalorado;
use Illuminate\Database\Eloquent\Model;

/** local_equipamento — atributo multivalorado, PK composta (id_local, equipamento). */
class LocalEquipamento extends Model
{
    use AtributoMultivalorado;

    protected $table = 'local_equipamento';

    public $timestamps = false;

    protected $fillable = ['id_local', 'equipamento'];
}
