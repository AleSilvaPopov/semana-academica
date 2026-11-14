<?php

namespace App\Console\Commands;

use App\Auth\Sha256Hasher;
use App\Models\Integrante;
use App\Models\Participante;
use Illuminate\Console\Command;

/**
 * Define uma senha conhecida para um usuário dos dados de teste (as originais são desconhecidas).
 *
 * Exemplos:
 *   php artisan senha:definir integrante 2023104791 semana2026
 *   php artisan senha:definir participante gabriela.prado11@gmail.com semana2026
 */
class DefinirSenha extends Command
{
    protected $signature = 'senha:definir
                            {tipo : integrante ou participante}
                            {login : RGA (integrante) ou e-mail (participante)}
                            {nova_senha : a nova senha}';

    protected $description = 'Define a senha (SHA-256) de um integrante ou participante';

    public function handle(Sha256Hasher $hasher): int
    {
        $tipo = strtolower($this->argument('tipo'));
        $login = trim($this->argument('login'));
        $senha = (string) $this->argument('nova_senha');

        if (strlen($senha) < 6) {
            $this->error('A senha deve ter pelo menos 6 caracteres.');

            return self::FAILURE;
        }

        $usuario = match ($tipo) {
            'integrante' => Integrante::find($login),
            'participante' => Participante::where('email', $login)->first(),
            default => null,
        };

        if (! in_array($tipo, ['integrante', 'participante'], true)) {
            $this->error('Tipo inválido. Use "integrante" ou "participante".');

            return self::FAILURE;
        }

        if (! $usuario) {
            $this->error($tipo === 'integrante' ? "Nenhum integrante com o RGA {$login}." : "Nenhum participante com o e-mail {$login}.");

            return self::FAILURE;
        }

        // Atualização direta da coluna (senha_hash não está no $fillable de propósito).
        $usuario->forceFill(['senha_hash' => $hasher->make($senha)])->save();

        $this->info("Senha de {$usuario->nome_completo} definida com sucesso.");

        if ($usuario instanceof Participante && ! $usuario->podeEntrar()) {
            $this->warn("Atenção: a conta está com status \"{$usuario->status_conta->rotulo()}\" e não consegue entrar no portal.");
        }

        return self::SUCCESS;
    }
}
