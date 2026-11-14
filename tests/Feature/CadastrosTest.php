<?php

namespace Tests\Feature;

use App\Models\Comissao;
use App\Models\Curso;
use App\Models\Integrante;
use App\Models\LocalEvento;
use App\Models\Patrocinio;
use App\Models\Presidencia;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CadastrosTest extends TestCase
{
    public function test_curso_recebe_id_max_mais_um(): void
    {
        $proximo = Curso::max('id_curso') + 1;

        $this->actingAs($this->presidente(), 'integrante')
            ->post('/admin/cursos', ['nome_curso' => 'Biotecnologia', 'faculdade' => 'FCBA'])
            ->assertRedirect("/admin/cursos/{$proximo}");

        $this->assertDatabaseHas('curso', ['id_curso' => $proximo, 'nome_curso' => 'Biotecnologia']);
    }

    public function test_unique_nome_curso_e_faculdade(): void
    {
        $this->actingAs($this->presidente(), 'integrante')
            ->post('/admin/cursos', ['nome_curso' => 'Matemática', 'faculdade' => 'Faculdade de Ciências Exatas e Tecnologia (FACET)'])
            ->assertSessionHasErrors('nome_curso');
    }

    public function test_excluir_curso_com_integrantes_mostra_mensagem_amigavel(): void
    {
        $this->actingAs($this->presidente(), 'integrante')
            ->from('/admin/cursos/1')
            ->delete('/admin/cursos/1')
            ->assertRedirect('/admin/cursos/1')
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'Não é possível excluir') && str_contains($m, 'integrantes'));

        $this->assertDatabaseHas('curso', ['id_curso' => 1]);
    }

    public function test_excluir_local_com_atividades_conta_os_vinculos(): void
    {
        $qtd = LocalEvento::find(2)->atividades()->count();

        $this->actingAs($this->presidente(), 'integrante')
            ->delete('/admin/locais/2')
            ->assertSessionHas('erro', "Não é possível excluir: existem {$qtd} atividades vinculadas.");
    }

    public function test_integrante_com_telefones_e_emails(): void
    {
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/integrantes', [
            'rga' => '2026999001', 'cpf' => '529.982.247-25', 'nome_completo' => 'Integrante Teste', 'id_curso' => 1,
            'funcao' => 'Apoio', 'data_ingresso' => '2026-03-01', 'id_coordenadoria' => 101,
            'senha' => 'segredo1', 'senha_confirmation' => 'segredo1',
            'telefones' => ['(67) 99999-1111', '(67) 3333-2222', ''], 'emails' => ['A@B.com', 'c@d.com'],
        ])->assertRedirect('/admin/integrantes/2026999001');

        $i = Integrante::find('2026999001');
        $this->assertEqualsCanonicalizing(['67999991111', '6733332222'], $i->telefones->pluck('telefone')->all());
        $this->assertEqualsCanonicalizing(['a@b.com', 'c@d.com'], $i->emails->pluck('email')->all());

        // Edição: apaga e reinsere a lista (atributo multivalorado)
        $this->put('/admin/integrantes/2026999001', [
            'cpf' => '52998224725', 'nome_completo' => 'Integrante Teste', 'id_curso' => 1, 'funcao' => 'Apoio',
            'data_ingresso' => '2026-03-01', 'telefones' => ['67988887777'], 'emails' => [],
        ])->assertRedirect('/admin/integrantes/2026999001');

        $i->refresh();
        $this->assertSame(['67988887777'], $i->telefones->pluck('telefone')->all());
        $this->assertCount(0, $i->emails);
    }

    public function test_data_ingresso_nao_pode_ser_posterior_a_coordenacao(): void
    {
        $pedro = $this->coordenador(); // coordena desde 2026-03-15
        $this->actingAs($this->presidente(), 'integrante')->put("/admin/integrantes/{$pedro->rga}", [
            'cpf' => $pedro->cpf, 'nome_completo' => $pedro->nome_completo, 'id_curso' => $pedro->id_curso,
            'funcao' => $pedro->funcao, 'data_ingresso' => '2026-04-01',
        ])->assertSessionHasErrors('data_ingresso');
    }

    public function test_edicao_com_presidencia_e_vice_diferente(): void
    {
        $dados = [
            'nome_evento' => 'Semana Acadêmica de Computação da UFGD', 'edicao' => 99,
            'data_inicio' => '2030-10-14', 'data_termino' => '2030-10-18', 'local_sede' => 'UFGD',
            'rga_presidente' => '2023104791', 'rga_vice' => '2023104791',
        ];

        $this->actingAs($this->presidente(), 'integrante')->post('/admin/comissoes', $dados)->assertSessionHasErrors('rga_vice');

        $dados['rga_vice'] = '2025109922';
        $this->post('/admin/comissoes', $dados)->assertSessionHasNoErrors();

        $edicao = Comissao::where('edicao', 99)->first();
        $this->assertSame(Comissao::where('id_comissao', '<>', $edicao->id_comissao)->max('id_comissao') + 1, $edicao->id_comissao);
        $this->assertDatabaseHas('presidencia', ['id_comissao' => $edicao->id_comissao, 'rga_presidente' => '2023104791', 'rga_vice' => '2025109922']);

        // Exclusão de edição sem vínculos remove a presidência junto
        $this->delete("/admin/comissoes/{$edicao->id_comissao}")->assertRedirect('/admin/comissoes');
        $this->assertNull(Presidencia::find($edicao->id_comissao));
    }

    public function test_check_de_datas_da_edicao(): void
    {
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/comissoes', [
            'nome_evento' => 'X', 'edicao' => 1, 'data_inicio' => '2030-10-18', 'data_termino' => '2030-10-14',
            'local_sede' => 'UFGD', 'rga_presidente' => '2023104791', 'rga_vice' => '2025109922',
        ])->assertSessionHasErrors('data_termino');
    }

    public function test_patrocinio_com_chave_composta(): void
    {
        $this->actingAs($this->presidente(), 'integrante');

        $this->post('/admin/patrocinios', [
            'nome_patrocinador' => 'Instituto Horizonte de Tecnologia', 'tipo_patrocinio' => 'financeiro',
            'descricao_patrocinio' => 'Cota bronze', 'valor' => '750.50',
        ])->assertRedirect('/admin/patrocinios');
        $this->assertSame('750.50', Patrocinio::daChave(10, 'Instituto Horizonte de Tecnologia')->first()->valor);

        // Duplicado na mesma edição é recusado (PK composta)
        $this->post('/admin/patrocinios', ['nome_patrocinador' => 'Instituto Horizonte de Tecnologia', 'tipo_patrocinio' => 'material', 'descricao_patrocinio' => 'x'])
            ->assertSessionHasErrors('nome_patrocinador');

        $url = '/admin/patrocinios/10/'.rawurlencode('Instituto Horizonte de Tecnologia');
        $this->put($url, ['tipo_patrocinio' => 'servico', 'descricao_patrocinio' => 'Consultoria', 'valor' => ''])->assertRedirect('/admin/patrocinios');
        $p = Patrocinio::daChave(10, 'Instituto Horizonte de Tecnologia')->first();
        $this->assertSame('servico', $p->tipo_patrocinio->value);
        $this->assertNull($p->valor);
        // A outra linha do mesmo patrocinador (edição 9) não foi tocada
        $this->assertSame('1500.00', Patrocinio::daChave(9, 'Instituto Horizonte de Tecnologia')->first()->valor);

        $this->delete($url)->assertRedirect('/admin/patrocinios');
        $this->assertFalse(Patrocinio::daChave(10, 'Instituto Horizonte de Tecnologia')->exists());
        $this->assertTrue(Patrocinio::daChave(9, 'Instituto Horizonte de Tecnologia')->exists());
    }

    public function test_patrocinio_valor_negativo_recusado(): void
    {
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/patrocinios', [
            'nome_patrocinador' => 'Instituto Horizonte de Tecnologia', 'tipo_patrocinio' => 'financeiro',
            'descricao_patrocinio' => 'x', 'valor' => '-1',
        ])->assertSessionHasErrors('valor');
    }

    public function test_ministrante_a_partir_de_participante(): void
    {
        $this->actingAs($this->presidente(), 'integrante');

        // Já é ministrante -> recusado
        $this->post('/admin/ministrantes', ['cpf' => '91699854300'])->assertSessionHasErrors('cpf');

        $this->post('/admin/ministrantes', ['cpf' => '02563421640', 'certificados' => ['Curso de Python', 'Curso de Python', 'AWS']])
            ->assertSessionHasErrors('certificados.1'); // repetido

        $this->post('/admin/ministrantes', ['cpf' => '02563421640', 'certificados' => ['Curso de Python', 'AWS']])
            ->assertRedirect('/admin/ministrantes/02563421640');
        $this->assertSame(2, DB::table('ministrante_certificado')->where('cpf', '02563421640')->count());
    }

    public function test_view_vw_quantidade_integrantes_e_somente_leitura(): void
    {
        $vw = \App\Models\VwQuantidadeIntegrantes::find(101);
        $this->assertSame(4, $vw->quantidade_integrantes);
        $this->expectException(\LogicException::class);
        $vw->save();
    }
}
