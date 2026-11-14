<?php

namespace App\Services;

use App\Auth\Sha256Hasher;
use App\Models\Participante;
use Illuminate\Support\Facades\DB;

/**
 * Cadastro e alteração de participantes (usado pelo painel e pelo portal).
 * Telefones são atributo multivalorado: apagados e reinseridos na mesma transação.
 */
class ParticipanteService
{
    public function __construct(private Sha256Hasher $hasher)
    {
    }

    public function criar(array $dados): Participante
    {
        return DB::transaction(function () use ($dados) {
            $participante = new Participante($dados);
            $participante->senha_hash = $this->hasher->make($dados['senha']);
            $participante->save();

            Multivalorado::sincronizar($participante->telefones(), 'telefone', $dados['telefones'] ?? []);

            return $participante;
        });
    }

    public function atualizar(Participante $participante, array $dados): Participante
    {
        return DB::transaction(function () use ($participante, $dados) {
            // A PK (cpf) não muda: as FKs que apontam para ela não têm ON UPDATE CASCADE.
            unset($dados['cpf']);
            $participante->fill($dados);

            if (! empty($dados['senha'])) {
                $participante->senha_hash = $this->hasher->make($dados['senha']);
            }

            $participante->save();

            if (array_key_exists('telefones', $dados)) {
                Multivalorado::sincronizar($participante->telefones(), 'telefone', $dados['telefones']);
            }

            return $participante;
        });
    }
}
