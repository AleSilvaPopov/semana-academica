<?php

namespace App\Models;

use App\Models\Concerns\AtributoMultivalorado;
use Illuminate\Database\Eloquent\Model;

/** integrante_email — atributo multivalorado, PK composta (rga, email). */
class IntegranteEmail extends Model
{
    use AtributoMultivalorado;

    protected $table = 'integrante_email';

    public $timestamps = false;

    protected $fillable = ['rga', 'email'];
}
