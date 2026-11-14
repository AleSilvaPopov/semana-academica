<?php

namespace Tests\Feature;

use App\Models\Atividade;
use App\Models\Comissao;
use App\Models\Coordenadoria;
use App\Models\Inscricao;
use App\Models\Participante;
use App\Models\Presidencia;
use App\Models\Tarefa;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegrasNegocioTest extends TestCase
{
    /** Cria uma edição futura com uma atividade de poucas vagas (os dados de teste já passaram). */
    private function atividadeFutura(int $vagas = 1, string $status = 'confirmada'): Atividade
    {
        $edicao = Comissao::criarComNovoId([
            'nome_evento' => 'Semana Teste', 'edicao' => 50, 'data_inicio' => now()->addMonth()->toDateString(),
            'data_termino' => now()->addMonth()->addDays(4)->toDateString(), 'local_sede' => 'UFGD',
        ]);
        Presidencia::create(['id_comissao' => $edicao->id_comissao, 'rga_presidente' => '2023104791', 'rga_vice' => '2025109922']);
        $coord = Coordenadoria::criarComNovoId([
            'nome' => 'Programação', 'descricao_atuacao' => 'x', 'id_comissao' => $edicao->id_comissao,
            'rga_coordenador' => '2025111220', 'data_inicio_coordenacao' => today()->toDateString(),
        ]);

        return Atividade::criarComNovoId([
            'nome' => 'Atividade de teste', 'descricao' => 'x', 'tipo' => 'workshop', 'duracao_minutos' => 90,
            'data_realizacao' => now()->addMonth()->setTime(9, 0), 'vagas' => $vagas, 'valor_inscricao' => 0,
            'status' => $status, 'id_local' => 4, 'id_coordenadoria' => $coord->id_coordenadoria,
        ]);
    }

    /* ----------------------------- Inscrições ----------------------------- */

    public function test_inscricao_lotacao_duplicidade_e_cancelamento(): void
    {
        $atividade = $this->atividadeFutura(vagas: 1);
        $gabriela = $this->participanteAtivo();
        $joao = Participante::where('email', 'joao.leal12@gmail.com')->first();

        $this->actingAs($gabriela, 'participante')->post("/atividades/{$atividade->id_atividade}/inscricao")
            ->assertSessionHas('sucesso');
        $this->assertTrue(Inscricao::daChave($gabriela->cpf, $atividade->id_atividade)->exists());

        // Duplicada
        $this->post("/atividades/{$atividade->id_atividade}/inscricao")->assertSessionHas('erro', fn ($m) => str_contains($m, 'já está inscrito'));

        // Lotada (vagas = 1)
        $this->actingAs($joao, 'participante')->post("/atividades/{$atividade->id_atividade}/inscricao")
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'lotada'));
        $this->assertSame(1, Inscricao::where('id_atividade', $atividade->id_atividade)->count());

        // Cancelamento antes da data libera a vaga
        $this->actingAs($gabriela, 'participante')->delete("/atividades/{$atividade->id_atividade}/inscricao")->assertSessionHas('sucesso');
        $this->actingAs($joao, 'participante')->post("/atividades/{$atividade->id_atividade}/inscricao")->assertSessionHas('sucesso');
    }

    public function test_so_inscreve_em_planejada_ou_confirmada(): void
    {
        $atividade = $this->atividadeFutura(vagas: 10, status: 'cancelada');

        $this->actingAs($this->participanteAtivo(), 'participante')->post("/atividades/{$atividade->id_atividade}/inscricao")
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'planejadas ou confirmadas'));
    }

    public function test_inscricao_deve_ser_anterior_a_atividade(): void
    {
        // Atividade 6 (20/10/2026) já aconteceu
        $this->actingAs(Participante::where('email', 'joao.leal12@gmail.com')->first(), 'participante')
            ->post('/atividades/6/inscricao')
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'encerradas'));
    }

    public function test_nao_cancela_inscricao_de_atividade_passada(): void
    {
        $this->actingAs($this->participanteAtivo(), 'participante')->delete('/atividades/5/inscricao')
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'já aconteceu'));
    }

    public function test_presenca_em_lote_e_certificado(): void
    {
        // Atividade 5 (19/10/2026, edição 10) — Pedro coordena Infraestrutura, não Programação: só leitura.
        $this->actingAs($this->coordenador(), 'integrante')->patch('/admin/inscricoes/5/presenca', ['presentes' => []])->assertForbidden();

        $this->actingAs($this->presidente(), 'integrante')
            ->patch('/admin/inscricoes/5/presenca', ['presentes' => ['02563421640', '73375433018']])
            ->assertSessionHas('sucesso', 'Presenças registradas: 2 participante(s) presente(s).');

        $this->assertTrue((bool) Inscricao::daChave('02563421640', 5)->value('presenca'));
        $this->assertFalse((bool) Inscricao::daChave('65414586853', 5)->value('presenca'));

        $this->actingAs($this->participanteAtivo(), 'participante')->get('/meus-certificados/5')
            ->assertOk()->assertSee('CERTIFICADO')->assertSee('***.634.216-**')->assertSee('1,5 hora');
        $this->actingAs(Participante::find('65414586853'), 'participante')->get('/meus-certificados/5')->assertNotFound();
    }

    public function test_presenca_nao_pode_ser_registrada_antes_do_dia(): void
    {
        $atividade = $this->atividadeFutura();
        $this->actingAs($this->presidente(), 'integrante')
            ->patch("/admin/inscricoes/{$atividade->id_atividade}/presenca", ['presentes' => []])
            ->assertSessionHas('erro', fn ($m) => str_contains($m, 'a partir do dia'));
    }

    /* ----------------------------- Atividades ----------------------------- */

    private function dadosAtividade(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Nova atividade', 'descricao' => 'Descrição', 'tipo' => 'palestra', 'status' => 'planejada',
            'duracao_minutos' => 60, 'data_realizacao' => '2026-10-21T10:00', 'vagas' => 30, 'valor_inscricao' => '0',
            'id_local' => 4, 'id_coordenadoria' => 101, 'ministrantes' => ['91699854300'],
        ], $extra);
    }

    public function test_coordenador_cadastra_atividade_da_propria_coordenadoria_com_ministrantes(): void
    {
        $this->actingAs($this->coordenador(), 'integrante')->post('/admin/atividades', $this->dadosAtividade())->assertSessionHasNoErrors();

        $nova = Atividade::where('nome', 'Nova atividade')->first();
        $this->assertSame(['91699854300'], $nova->ministrantes->pluck('cpf')->all());

        // Não pode usar coordenadoria de outra pessoa
        $this->post('/admin/atividades', $this->dadosAtividade(['id_coordenadoria' => 104]))->assertSessionHasErrors('id_coordenadoria');
    }

    public function test_vagas_nao_passam_da_capacidade_do_local(): void
    {
        // Local 4 = LAB-205, capacidade 30
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/atividades', $this->dadosAtividade(['vagas' => 31]))
            ->assertSessionHasErrors('vagas');
    }

    public function test_data_da_atividade_dentro_da_edicao(): void
    {
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/atividades', $this->dadosAtividade(['data_realizacao' => '2026-10-30T10:00']))
            ->assertSessionHasErrors('data_realizacao');
    }

    public function test_vagas_nao_ficam_abaixo_dos_inscritos(): void
    {
        $a = Atividade::find(5); // 12 inscritos
        $this->actingAs($this->presidente(), 'integrante')->put('/admin/atividades/5', $this->dadosAtividade([
            'nome' => $a->nome, 'vagas' => 5, 'id_local' => 10, 'id_coordenadoria' => 104, 'data_realizacao' => '2026-10-19T19:00',
        ]))->assertSessionHasErrors('vagas');
    }

    public function test_capacidade_do_local_nao_fica_abaixo_das_vagas(): void
    {
        $local = \App\Models\LocalEvento::find(10); // atividade 5 tem 380 vagas
        $this->actingAs($this->presidente(), 'integrante')->put('/admin/locais/10', [
            'tipo' => 'auditorio', 'faculdade' => $local->faculdade, 'bloco' => $local->bloco, 'numero' => $local->numero, 'capacidade' => 100,
        ])->assertSessionHasErrors('capacidade');
    }

    /* ------------------------- Tarefas e triggers ------------------------- */

    public function test_trigger_impede_tarefa_de_quem_nao_e_coordenador(): void
    {
        try {
            DB::table('tarefa')->insert([
                'id_tarefa' => 999, 'descricao' => 'x', 'data_criacao' => '2026-09-01', 'prazo_final' => '2026-09-02',
                'status' => 'pendente', 'rga_criador' => '2026116444', // Gustavo não coordena nada
            ]);
            $this->fail('O trigger trg_tarefa_criador_bi deveria ter bloqueado.');
        } catch (QueryException $e) {
            $this->assertSame('45000', $e->errorInfo[0]);
            $this->assertSame('Somente coordenadores podem criar tarefas.', $e->errorInfo[2]);
        }

        // BEFORE UPDATE: trocar o criador para um não coordenador também é bloqueado
        $this->expectException(QueryException::class);
        DB::table('tarefa')->where('id_tarefa', 4)->update(['rga_criador' => '2026116444']);
    }

    public function test_coordenador_cria_tarefa_com_responsaveis(): void
    {
        $this->actingAs($this->coordenador(), 'integrante')->post('/admin/tarefas', [
            'descricao' => 'Testar projetores', 'data_criacao' => '2026-09-20', 'prazo_final' => '2026-10-01',
            'status' => 'pendente', 'rga_criador' => '2023104791', // ignorado: coordenador cria em nome próprio
            'responsaveis' => ['2026116444', '2026121680'],
        ])->assertSessionHasNoErrors();

        $t = Tarefa::where('descricao', 'Testar projetores')->first();
        $this->assertSame('2026113855', $t->rga_criador);
        $this->assertEqualsCanonicalizing(['2026116444', '2026121680'], $t->responsaveis->pluck('rga')->all());
        $this->assertTrue(Tarefa::daEdicao(10)->whereKey($t->id_tarefa)->exists());
    }

    public function test_integrante_comum_nao_cria_tarefa_mas_altera_status_das_suas(): void
    {
        $gustavo = $this->comum();
        $this->actingAs($gustavo, 'integrante')->get('/admin/tarefas/create')->assertForbidden();
        $this->post('/admin/tarefas', ['descricao' => 'x'])->assertForbidden();

        // Responsável pela tarefa 4: pode alterar o status (dispara o trigger BEFORE UPDATE, que aceita)
        $this->patch('/admin/tarefas/4/status', ['status' => 'concluida'])->assertSessionHas('sucesso');
        $this->assertSame('concluida', Tarefa::find(4)->status->value);

        // Não é responsável pela tarefa 6
        $this->patch('/admin/tarefas/6/status', ['status' => 'concluida'])->assertForbidden();
    }

    public function test_prazo_final_nao_antes_da_criacao(): void
    {
        $this->actingAs($this->coordenador(), 'integrante')->post('/admin/tarefas', [
            'descricao' => 'x', 'data_criacao' => '2026-09-20', 'prazo_final' => '2026-09-10', 'status' => 'pendente',
        ])->assertSessionHasErrors('prazo_final');
    }

    public function test_data_inicio_coordenacao_nao_anterior_ao_ingresso(): void
    {
        $this->actingAs($this->presidente(), 'integrante')->post('/admin/coordenadorias', [
            'nome' => 'Nova coord', 'descricao_atuacao' => 'x', 'rga_coordenador' => '2026117743', // ingresso 2026-03-09
            'data_inicio_coordenacao' => '2026-01-01',
        ])->assertSessionHasErrors('data_inicio_coordenacao');
    }

    /* ----------------------------- Permissões ----------------------------- */

    public function test_permissoes_por_papel(): void
    {
        // Integrante comum: lê tudo, não cadastra nada global
        $this->actingAs($this->comum(), 'integrante');
        $this->get('/admin/cursos')->assertOk()->assertDontSee('Novo curso');
        $this->get('/admin/cursos/create')->assertForbidden();
        $this->delete('/admin/cursos/1')->assertForbidden();
        $this->get('/admin/patrocinios/create')->assertForbidden();

        // Coordenador: cria atividades, mas não coordenadorias nem patrocínios
        $this->actingAs($this->coordenador(), 'integrante');
        $this->get('/admin/atividades/create')->assertOk();
        $this->get('/admin/coordenadorias/create')->assertForbidden();
        $this->get('/admin/atividades/6/edit')->assertForbidden(); // atividade de outra coordenadoria

        // Presidente: tudo
        $this->actingAs($this->presidente(), 'integrante');
        $this->get('/admin/coordenadorias/create')->assertOk();
        $this->get('/admin/cursos/create')->assertOk();
    }
}
