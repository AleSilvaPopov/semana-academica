<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\PerfilRequest;
use App\Services\ParticipanteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** "Meu perfil" do participante: dados pessoais, telefones e senha. */
class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        $participante = $request->user('participante')->load('telefones');

        return view('portal.perfil', compact('participante'));
    }

    public function update(PerfilRequest $request, ParticipanteService $servico): RedirectResponse
    {
        $dados = collect($request->validated())->except('senha_atual')->all();
        $servico->atualizar($request->user('participante'), $dados);

        return redirect()->route('portal.perfil')->with('sucesso', 'Perfil atualizado com sucesso.');
    }
}
