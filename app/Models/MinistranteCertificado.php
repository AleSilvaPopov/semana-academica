<?php

namespace App\Models;

use App\Models\Concerns\AtributoMultivalorado;
use Illuminate\Database\Eloquent\Model;

/** ministrante_certificado — atributo multivalorado, PK composta (cpf, certificado). */
class MinistranteCertificado extends Model
{
    use AtributoMultivalorado;

    protected $table = 'ministrante_certificado';

    public $timestamps = false;

    protected $fillable = ['cpf', 'certificado'];
}
