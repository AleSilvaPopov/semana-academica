<?php

use App\Models\Integrante;
use App\Models\Participante;

/*
 * Autenticação com DOIS guards e sem a tabela "users" (que não existe no banco):
 *  - integrante   -> painel da organização (/admin), login por RGA + senha
 *  - participante -> portal do participante (/), login por e-mail + senha
 *
 * Os dois usam o driver "sha256" (App\Auth\Sha256UserProvider), registrado no
 * AppServiceProvider, porque as senhas do banco estão em SHA-256 hexadecimal.
 */
return [

    'defaults' => [
        'guard' => 'participante',
        'passwords' => 'participantes',
    ],

    'guards' => [
        'integrante' => [
            'driver' => 'session',
            'provider' => 'integrantes',
        ],
        'participante' => [
            'driver' => 'session',
            'provider' => 'participantes',
        ],
    ],

    'providers' => [
        'integrantes' => [
            'driver' => 'sha256',
            'model' => Integrante::class,
        ],
        'participantes' => [
            'driver' => 'sha256',
            'model' => Participante::class,
        ],
    ],

    /*
     * Não há tabela password_reset_tokens. O "Esqueci minha senha" do portal usa
     * links assinados (URL::temporarySignedRoute) e não depende desta configuração.
     */
    'passwords' => [
        'participantes' => [
            'provider' => 'participantes',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
