<?php

namespace App\Services;

use App\Auth\Sha256Hasher;
use App\Models\Integrante;
use Illuminate\Support\Facades\DB;

/** Cadastro/alteração de integrante com telefones e e-mails (multivalorados) numa só transação. */
class IntegranteService
{
    public function __construct(private Sha256Hasher $hasher)
    {
    }

    public function salvar(Integrante $integrante, array $dados): Integrante
    {
        return DB::transaction(function () use ($integrante, $dados) {
            if ($integrante->exists) {
                unset($dados['rga']); // PK somente leitura (FKs sem ON UPDATE CASCADE)
            }

            $integrante->fill(collect($dados)->except(['senha', 'telefones', 'emails'])->all());

            if (! empty($dados['senha'])) {
                // Mesmo formato do banco: SHA-256 hexadecimal (ver App\Auth\Sha256Hasher).
                $integrante->senha_hash = $this->hasher->make($dados['senha']);
            }

            $integrante->save();

            Multivalorado::sincronizar($integrante->telefones(), 'telefone', $dados['telefones'] ?? []);
            Multivalorado::sincronizar($integrante->emails(), 'email', $dados['emails'] ?? []);

            return $integrante;
        });
    }
}
