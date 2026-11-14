<?php

namespace App\Auth;

use Illuminate\Contracts\Hashing\Hasher;

/**
 * Hash de senha compatível com o banco semana_academica.
 *
 * As colunas integrante.senha_hash e participante.senha_hash são CHAR(64) e os dados
 * de teste foram gravados como SHA-256 em hexadecimal (equivalente a SHA2('senha', 256)
 * do MySQL). Por isso NÃO usamos o bcrypt padrão do Laravel: ele gera 60 caracteres
 * em outro formato e não reconheceria as senhas existentes.
 *
 * Toda a lógica de senha fica isolada nesta classe. Para migrar para bcrypt no futuro,
 * basta trocar a implementação registrada no AppServiceProvider (e aumentar a coluna).
 *
 * Observação de segurança: SHA-256 puro (sem salt) é mais fraco que bcrypt/argon2;
 * foi mantido apenas para compatibilidade com o banco entregue.
 */
class Sha256Hasher implements Hasher
{
    public function info($hashedValue): array
    {
        return ['algo' => 'sha256', 'algoName' => 'sha256', 'options' => []];
    }

    public function make($value, array $options = []): string
    {
        return hash('sha256', (string) $value);
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        if ($value === null || $hashedValue === null || $hashedValue === '') {
            return false;
        }

        // hash_equals compara em tempo constante (evita ataque de temporização).
        return hash_equals(strtolower(trim((string) $hashedValue)), hash('sha256', (string) $value));
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        // Nunca re-hasheia: o formato precisa continuar compatível com o CHAR(64) do banco.
        return false;
    }
}
