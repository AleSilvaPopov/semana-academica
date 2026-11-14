<?php

namespace Tests\Feature;

use App\Auth\Sha256Hasher;
use App\Models\Integrante;
use App\Models\Participante;
use Tests\TestCase;

class AutenticacaoTest extends TestCase
{
    private function definirSenha(Integrante|Participante $usuario, string $senha = 'teste123'): void
    {
        $usuario->forceFill(['senha_hash' => hash('sha256', $senha)])->save();
    }

    public function test_hash_sha256_compativel_com_o_mysql(): void
    {
        $hasher = new Sha256Hasher();
        // SELECT SHA2('semana2026', 256) no MySQL
        $this->assertSame(hash('sha256', 'semana2026'), $hasher->make('semana2026'));
        $this->assertTrue($hasher->check('semana2026', strtoupper($hasher->make('semana2026'))));
        $this->assertFalse($hasher->check('errada', $hasher->make('semana2026')));
        $this->assertSame(64, strlen($hasher->make('x')));
    }

    public function test_integrante_entra_com_rga_e_senha(): void
    {
        $this->definirSenha($this->presidente());

        $this->post('/admin/login', ['rga' => '2023104791', 'senha' => 'teste123'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->presidente(), 'integrante');
    }

    public function test_integrante_com_senha_errada_nao_entra(): void
    {
        $this->definirSenha($this->presidente());

        $this->post('/admin/login', ['rga' => '2023104791', 'senha' => 'errada'])->assertSessionHasErrors('rga');
        $this->assertGuest('integrante');
    }

    public function test_participante_ativo_entra_pelo_email(): void
    {
        $this->definirSenha($this->participanteAtivo());

        $this->post('/entrar', ['email' => 'gabriela.prado11@gmail.com', 'senha' => 'teste123'])->assertRedirect('/');
        $this->assertAuthenticated('participante');
    }

    public function test_participante_bloqueado_ou_inativo_nao_entra(): void
    {
        foreach (['rodrigo.nogueira24@gmail.com', 'clara.fagundes21@gmail.com'] as $email) {
            $this->definirSenha(Participante::where('email', $email)->first());

            $this->post('/entrar', ['email' => $email, 'senha' => 'teste123'])->assertSessionHasErrors('email');
            $this->assertGuest('participante');
        }
    }

    public function test_rotas_protegidas_redirecionam_para_o_login_certo(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/minhas-inscricoes')->assertRedirect('/entrar');
    }

    public function test_cadastro_de_conta_no_portal(): void
    {
        $this->post('/cadastro', [
            'cpf' => '529.982.247-25', 'nome_completo' => 'Fulana de Teste', 'email' => 'Fulana@Teste.com',
            'senha' => 'segredo1', 'senha_confirmation' => 'segredo1', 'pais' => 'Brasil',
            'data_nascimento' => '2000-01-01', 'tipo_participante' => 'discente',
            'instituicao_origem' => 'UFGD', 'telefones' => ['(67) 99999-0000'],
        ])->assertRedirect('/programacao');

        $p = Participante::find('52998224725');
        $this->assertNotNull($p);
        $this->assertSame('fulana@teste.com', $p->email);
        $this->assertSame(hash('sha256', 'segredo1'), $p->senha_hash);
        $this->assertSame(['67999990000'], $p->telefones->pluck('telefone')->all());
    }

    public function test_cadastro_rejeita_cpf_invalido(): void
    {
        $this->post('/cadastro', ['cpf' => '111.111.111-11'])->assertSessionHasErrors('cpf');
        $this->post('/cadastro', ['cpf' => '529.982.247-24'])->assertSessionHasErrors('cpf');
    }

    public function test_comando_senha_definir(): void
    {
        $this->artisan('senha:definir', ['tipo' => 'integrante', 'login' => '2026116444', 'nova_senha' => 'nova12345'])->assertSuccessful();
        $this->assertSame(hash('sha256', 'nova12345'), Integrante::find('2026116444')->senha_hash);

        $this->artisan('senha:definir', ['tipo' => 'participante', 'login' => 'naoexiste@x.com', 'nova_senha' => 'nova12345'])->assertFailed();
    }
}
