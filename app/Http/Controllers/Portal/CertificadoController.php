<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Services\CertificadoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** "Meus certificados": atividades com presenca = 1 do participante logado. */
class CertificadoController extends Controller
{
    public function index(Request $request): View
    {
        $inscricoes = $request->user('participante')->inscricoes()
            ->where('presenca', true)
            ->with('atividade.coordenadoria.comissao')
            ->get()
            ->sortByDesc(fn ($i) => $i->atividade->data_realizacao);

        return view('portal.certificados', compact('inscricoes'));
    }

    public function show(Request $request, Atividade $atividade, CertificadoService $servico): View
    {
        // dados() devolve 404 se o participante logado não tiver presença na atividade.
        return view('certificado', $servico->dados($request->user('participante'), $atividade) + ['voltar' => route('portal.certificados')]);
    }
}
