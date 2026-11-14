<?php

namespace Tests;

use App\Models\Integrante;
use App\Models\Participante;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Os testes usam o banco semana_academica real (o mesmo do .env) e cada teste roda
 * dentro de uma transação desfeita no final (DatabaseTransactions): nada fica gravado.
 */
abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Os testes usam a 10ª edição do script.sql (independe de o dados_demo.sql ter sido importado).
        $this->withSession(['admin.edicao_id' => 10]);
    }

    /**
     * Simula uma requisição nova a cada chamada: limpa os serviços "scoped" (EdicaoAtual,
     * Permissoes), que guardam dados só durante uma requisição.
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->app->forgetScopedInstances();

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    protected function presidente(): Integrante
    {
        return Integrante::findOrFail('2023104791'); // Presidente da 10ª edição
    }

    protected function coordenador(): Integrante
    {
        return Integrante::findOrFail('2026113855'); // Coordenador de Infraestrutura (10ª edição)
    }

    protected function comum(): Integrante
    {
        return Integrante::findOrFail('2026116444'); // Integrante comum
    }

    protected function participanteAtivo(): Participante
    {
        return Participante::where('email', 'gabriela.prado11@gmail.com')->firstOrFail();
    }
}
