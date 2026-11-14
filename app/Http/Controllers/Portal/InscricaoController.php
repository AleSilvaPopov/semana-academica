<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Services\InscricaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Inscrição, cancelamento e "Minhas inscrições" do participante logado. */
class InscricaoController extends Controller
{
    public function index(Request $request): View
    {
        $inscricoes = $request->user('participante')->inscricoes()
            ->with(['atividade' => fn ($q) => $q->with('local', 'coordenadoria.comissao')])
            ->get()
            ->sortByDesc(fn ($i) => $i->atividade->data_realizacao);

        return view('portal.inscricoes', [
            'futuras' => $inscricoes->filter(fn ($i) => $i->atividade->data_realizacao->isFuture())->sortBy(fn ($i) => $i->atividade->data_realizacao),
            'passadas' => $inscricoes->reject(fn ($i) => $i->atividade->data_realizacao->isFuture()),
        ]);
    }

    public function store(Request $request, Atividade $atividade, InscricaoService $servico): RedirectResponse
    {
        $servico->inscrever($request->user('participante'), $atividade);

        return redirect()->route('portal.atividades.show', $atividade)
            ->with('sucesso', 'Inscrição confirmada em "'.$atividade->nome.'". Até lá!');
    }

    public function destroy(Request $request, Atividade $atividade, InscricaoService $servico): RedirectResponse
    {
        $servico->cancelar($request->user('participante'), $atividade);

        return back()->with('sucesso', 'Inscrição em "'.$atividade->nome.'" cancelada.');
    }
}
