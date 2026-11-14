<?php

use App\Http\Controllers\Admin\EdicaoController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PARTE 1 — Estrutura: layout navegável com páginas placeholder.
|--------------------------------------------------------------------------
*/

Route::get('/status', StatusController::class)->name('status');

// Portal do participante
Route::name('portal.')->group(function () {
    Route::view('/', 'portal.em-construcao', ['titulo' => 'Semana Acadêmica de Computação'])->name('home');
    Route::view('/programacao', 'portal.em-construcao', ['titulo' => 'Programação'])->name('programacao');
    Route::view('/entrar', 'portal.em-construcao', ['titulo' => 'Entrar'])->name('login');
    Route::view('/cadastro', 'portal.em-construcao', ['titulo' => 'Criar conta'])->name('cadastro');
});

// Painel da organização
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'portal.em-construcao', ['titulo' => 'Login da organização'])->name('login');
    Route::post('/edicao', [EdicaoController::class, 'selecionar'])->name('edicao.selecionar');

    $telas = [
        '' => ['dashboard', 'Dashboard'],
        'comissoes' => ['comissoes.index', 'Edições'],
        'coordenadorias' => ['coordenadorias.index', 'Coordenadorias'],
        'atividades' => ['atividades.index', 'Atividades'],
        'inscricoes' => ['inscricoes.index', 'Inscrições e presença'],
        'certificados' => ['certificados.index', 'Certificados'],
        'tarefas' => ['tarefas.index', 'Tarefas'],
        'minhas-tarefas' => ['minhas-tarefas', 'Minhas tarefas'],
        'patrocinios' => ['patrocinios.index', 'Patrocínios'],
        'integrantes' => ['integrantes.index', 'Integrantes'],
        'cursos' => ['cursos.index', 'Cursos'],
        'participantes' => ['participantes.index', 'Participantes'],
        'ministrantes' => ['ministrantes.index', 'Ministrantes'],
        'locais' => ['locais.index', 'Locais'],
        'patrocinadores' => ['patrocinadores.index', 'Patrocinadores'],
    ];

    foreach ($telas as $uri => [$nome, $titulo]) {
        Route::view('/'.$uri, 'admin.em-construcao', ['titulo' => $titulo])->name($nome);
    }
});
