<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Login do painel (guard "integrante"): RGA + senha. */
class AuthController extends Controller
{
    public function formulario(): View
    {
        return view('admin.auth.login');
    }

    public function entrar(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'rga' => ['required', 'string', 'max:20'],
            'senha' => ['required', 'string'],
        ]);

        // No máximo 5 tentativas por minuto para o mesmo RGA + IP.
        $chave = 'login-integrante:'.Str::lower($dados['rga']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            throw ValidationException::withMessages([
                'rga' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($chave)]),
            ]);
        }

        // "password" é a chave que o UserProvider usa para conferir o SHA-256 de senha_hash.
        if (! Auth::guard('integrante')->attempt(['rga' => trim($dados['rga']), 'password' => $dados['senha']])) {
            RateLimiter::hit($chave);

            throw ValidationException::withMessages(['rga' => 'RGA ou senha incorretos.']);
        }

        RateLimiter::clear($chave);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('sucesso', 'Bem-vindo(a), '.Str::before(Auth::guard('integrante')->user()->nome_completo, ' ').'!');
    }

    public function sair(Request $request): RedirectResponse
    {
        Auth::guard('integrante')->logout();
        $request->session()->forget('admin.edicao_id');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('sucesso', 'Você saiu do painel.');
    }
}
