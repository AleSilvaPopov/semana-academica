<?php

namespace App\Models;

use App\Models\Concerns\AtributoMultivalorado;
use Illuminate\Database\Eloquent\Model;

/** integrante_telefone — atributo multivalorado, PK composta (rga, telefone). */
class IntegranteTelefone extends Model
{
    use AtributoMultivalorado;

    protected $table = 'integrante_telefone';

    public $timestamps = false;

    protected $fillable = ['rga', 'telefone'];
}
