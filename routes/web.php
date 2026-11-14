<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Portal;
use App\Http\Controllers\StatusController;
use App\Http\Middleware\ParticipanteAtivo;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Status da conexão com o banco (APP_DEBUG=true ou presidente/vice logado)
|--------------------------------------------------------------------------
*/
Route::get('/status', StatusController::class)->name('status');

/*
|--------------------------------------------------------------------------
| PORTAL DO PARTICIPANTE (/) — guard "participante"
|--------------------------------------------------------------------------
*/
Route::name('portal.')->middleware(ParticipanteAtivo::class)->group(function () {
    // Páginas públicas
    Route::get('/', Portal\HomeController::class)->name('home');
    Route::get('/programacao', [Portal\ProgramacaoController::class, 'index'])->name('programacao');
    Route::get('/atividades/{atividade}', [Portal\ProgramacaoController::class, 'show'])->name('atividades.show');

    // Visitantes: login, cadastro e "Esqueci minha senha"
    Route::middleware('guest:participante')->group(function () {
        Route::get('/entrar', [Portal\AuthController::class, 'formularioLogin'])->name('login');
        Route::post('/entrar', [Portal\AuthController::class, 'entrar']);
        Route::get('/cadastro', [Portal\AuthController::class, 'formularioCadastro'])->name('cadastro');
        Route::post('/cadastro', [Portal\AuthController::class, 'cadastrar']);
        Route::get('/esqueci-senha', [Portal\AuthController::class, 'formularioEsqueci'])->name('senha.esqueci');
        Route::post('/esqueci-senha', [Portal\AuthController::class, 'enviarLink']);
        // Link assinado e com validade (middleware "signed"): não precisa de tabela de tokens.
        Route::get('/redefinir-senha/{participante}', [Portal\AuthController::class, 'formularioRedefinir'])->middleware('signed')->name('senha.redefinir');
        Route::post('/redefinir-senha/{participante}', [Portal\AuthController::class, 'redefinir'])->middleware('signed');
    });

    // Área do participante logado
    Route::middleware('auth:participante')->group(function () {
        Route::post('/sair', [Portal\AuthController::class, 'sair'])->name('logout');
        Route::post('/atividades/{atividade}/inscricao', [Portal\InscricaoController::class, 'store'])->name('inscricoes.store');
        Route::delete('/atividades/{atividade}/inscricao', [Portal\InscricaoController::class, 'destroy'])->name('inscricoes.destroy');
        Route::get('/minhas-inscricoes', [Portal\InscricaoController::class, 'index'])->name('inscricoes');
        Route::get('/meus-certificados', [Portal\CertificadoController::class, 'index'])->name('certificados');
        Route::get('/meus-certificados/{atividade}', [Portal\CertificadoController::class, 'show'])->name('certificados.show');
        Route::get('/meu-perfil', [Portal\PerfilController::class, 'edit'])->name('perfil');
        Route::put('/meu-perfil', [Portal\PerfilController::class, 'update'])->name('perfil.update');
    });
});

/*
|--------------------------------------------------------------------------
| PAINEL DA ORGANIZAÇÃO (/admin) — guard "integrante"
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:integrante')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'formulario'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'entrar']);
    });

    Route::middleware('auth:integrante')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'sair'])->name('logout');
        Route::post('/edicao', [Admin\EdicaoController::class, 'selecionar'])->name('edicao.selecionar');

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // Edição selecionada
        Route::resource('comissoes', Admin\ComissaoController::class)->parameters(['comissoes' => 'comissao']);
        Route::resource('coordenadorias', Admin\CoordenadoriaController::class)->parameters(['coordenadorias' => 'coordenadoria']);
        Route::resource('atividades', Admin\AtividadeController::class)->parameters(['atividades' => 'atividade']);

        Route::get('/minhas-tarefas', [Admin\TarefaController::class, 'minhas'])->name('minhas-tarefas');
        Route::patch('/tarefas/{tarefa}/status', [Admin\TarefaController::class, 'alterarStatus'])->name('tarefas.status');
        Route::resource('tarefas', Admin\TarefaController::class)->parameters(['tarefas' => 'tarefa']);

        // Patrocínio: PK composta (id_comissao + nome_patrocinador) -> as duas chaves na URL.
        Route::controller(Admin\PatrocinioController::class)->prefix('patrocinios')->name('patrocinios.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{comissao}/{patrocinador}', 'show')->name('show');
            Route::get('/{comissao}/{patrocinador}/edit', 'edit')->name('edit');
            Route::put('/{comissao}/{patrocinador}', 'update')->name('update');
            Route::delete('/{comissao}/{patrocinador}', 'destroy')->name('destroy');
        });

        // Inscrição: PK composta (cpf + id_atividade).
        Route::controller(Admin\InscricaoController::class)->prefix('inscricoes')->name('inscricoes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{atividade}', 'show')->name('show');
            Route::post('/{atividade}', 'store')->name('store');
            Route::patch('/{atividade}/presenca', 'presenca')->name('presenca');
            Route::delete('/{atividade}/{participante}', 'destroy')->name('destroy');
        });

        Route::controller(Admin\CertificadoController::class)->prefix('certificados')->name('certificados.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{atividade}', 'show')->name('show');
            Route::get('/{atividade}/{participante}', 'emitir')->name('emitir');
        });

        // Cadastros gerais
        Route::resource('integrantes', Admin\IntegranteController::class)->parameters(['integrantes' => 'integrante']);
        Route::resource('cursos', Admin\CursoController::class)->parameters(['cursos' => 'curso']);
        Route::resource('participantes', Admin\ParticipanteController::class)->parameters(['participantes' => 'participante']);
        Route::resource('ministrantes', Admin\MinistranteController::class)->parameters(['ministrantes' => 'ministrante']);
        Route::resource('locais', Admin\LocalController::class)->parameters(['locais' => 'local']);
        Route::resource('patrocinadores', Admin\PatrocinadorController::class)->parameters(['patrocinadores' => 'patrocinador']);
    });
});
