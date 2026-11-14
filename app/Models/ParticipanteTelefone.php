<?php

namespace App\Models;

use App\Models\Concerns\AtributoMultivalorado;
use Illuminate\Database\Eloquent\Model;

/** participante_telefone — atributo multivalorado, PK composta (cpf, telefone). */
class ParticipanteTelefone extends Model
{
    use AtributoMultivalorado;

    protected $table = 'participante_telefone';

    public $timestamps = false;

    protected $fillable = ['cpf', 'telefone'];
}
