<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comissao;
use App\Services\EdicaoAtual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Troca a edição selecionada no cabeçalho do painel (guardada na sessão). */
class EdicaoController extends Controller
{
    public function selecionar(Request $request, EdicaoAtual $edicaoAtual): RedirectResponse
    {
        $dados = $request->validate([
            'id_comissao' => ['required', 'integer', 'exists:comissao_organizadora,id_comissao'],
        ]);

        $edicaoAtual->selecionar((int) $dados['id_comissao']);
        $edicao = Comissao::find($dados['id_comissao']);

        // Volta para a listagem da tela atual (detalhes de outra edição podem não fazer sentido).
        $anterior = url()->previous();
        $destino = preg_replace('#^(.*?/admin/[a-z\-]+)(/.*)?$#', '$1', parse_url($anterior, PHP_URL_PATH) ?? '');

        return redirect($destino ?: route('admin.dashboard'))
            ->with('sucesso', 'Edição selecionada: '.$edicao->titulo.'.');
    }
}
