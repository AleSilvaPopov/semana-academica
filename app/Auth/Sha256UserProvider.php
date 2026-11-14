<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * UserProvider dos dois guards (integrante e participante).
 *
 * Reaproveita o EloquentUserProvider (busca por RGA ou e-mail) e só troca a forma de
 * validar a senha: SHA-256 hexadecimal, comparado com hash_equals (ver Sha256Hasher).
 * Registrado no AppServiceProvider como driver "sha256" e usado em config/auth.php.
 */
class Sha256UserProvider extends EloquentUserProvider
{
    public function __construct(Sha256Hasher $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $senha = $credentials['password'] ?? null;

        if ($senha === null) {
            return false;
        }

        return $this->hasher->check($senha, $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // Mantém o hash no formato SHA-256 do banco; nada a fazer.
    }
}
