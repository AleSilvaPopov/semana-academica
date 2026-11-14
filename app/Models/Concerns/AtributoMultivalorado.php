<?php

namespace App\Models\Concerns;

/**
 * Base dos models das tabelas de atributos multivalorados
 * (integrante_telefone, integrante_email, participante_telefone,
 * ministrante_certificado e local_equipamento).
 *
 * Essas tabelas têm PK COMPOSTA (dono + valor), que o Eloquent não suporta.
 * Como nunca alteramos uma linha isolada, desligamos a PK no model: as linhas
 * são sempre apagadas e reinseridas em bloco (ver App\Services\Multivalorado).
 */
trait AtributoMultivalorado
{
    public function initializeAtributoMultivalorado(): void
    {
        $this->primaryKey = null;
        $this->incrementing = false;
        $this->timestamps = false;
    }
}
