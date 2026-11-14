<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Se a conta do participante for bloqueada/inativada enquanto ele está logado,
 * a sessão é encerrada na próxima requisição (regra: conta inativa ou bloqueada não acessa).
 */
class ParticipanteAtivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $participante = Auth::guard('participante')->user();

        if ($participante && ! $participante->podeEntrar()) {
            Auth::guard('participante')->logout();
            $request->session()->regenerateToken();

            return redirect()->route('portal.login')->with('erro', 'Sua conta não está ativa. Entre em contato com a organização.');
        }

        return $next($request);
    }
}
