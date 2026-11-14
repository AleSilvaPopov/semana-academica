<?php

use App\Exceptions\ErrosBanco;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Visitante sem login: manda para o login da área certa.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('admin', 'admin/*') ? route('admin.login') : route('portal.login')
        );

        // Já logado tentando abrir a tela de login: manda para o início da área.
        $middleware->redirectUsersTo(
            fn (Request $request) => $request->is('admin', 'admin/*') ? route('admin.dashboard') : route('portal.home')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Erros do MySQL (FK 1451, trigger 45000, CHECK, UNIQUE) viram mensagens amigáveis.
        $exceptions->render(fn (QueryException $e, Request $request) => ErrosBanco::renderizar($e, $request));
    })->create();
