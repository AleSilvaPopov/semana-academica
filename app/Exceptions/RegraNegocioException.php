<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Violação de regra de negócio (lotação, prazo, status...). O Laravel chama render()
 * automaticamente: o usuário volta para a tela anterior com a mensagem em vermelho.
 */
class RegraNegocioException extends Exception
{
    public function render(): RedirectResponse
    {
        return redirect()->back()->withInput()->with('erro', $this->getMessage());
    }
}
