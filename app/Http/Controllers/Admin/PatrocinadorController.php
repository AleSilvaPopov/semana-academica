<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatrocinadorRequest;
use App\Models\Patrocinador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Cadastro global de patrocinadores. A PK é o nome (string), usado também na URL. */
class PatrocinadorController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = Patrocinador::query()
            ->withCount('patrocinios')
            ->withSum('patrocinios as total_financeiro', 'valor');

        $this->buscar($query, $request->query('q'), ['nome', 'observacoes']);
        $query->when($request->query('situacao') === 'com', fn ($q) => $q->has('patrocinios'))
            ->when($request->query('situacao') === 'sem', fn ($q) => $q->doesntHave('patrocinios'));
        $this->ordenar($query, $request, [
            'nome' => 'nome',
            'patrocinios' => 'patrocinios_count',
            'total' => 'total_financeiro',
        ], 'nome');

        return view('admin.patrocinadores.index', [
            'patrocinadores' => $query->paginate($this->porPagina)->withQueryString(),
        ]);
    }

    public function show(Patrocinador $patrocinador): View
    {
        $patrocinador->load(['patrocinios' => fn ($q) => $q->with('comissao')
            ->join('comissao_organizadora as co', 'co.id_comissao', '=', 'patrocinio.id_comissao')
            ->orderByDesc('co.data_inicio')
            ->select('patrocinio.*')]);

        return view('admin.patrocinadores.show', compact('patrocinador'));
    }

    public function create(): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.patrocinadores.create', ['patrocinador' => new Patrocinador()]);
    }

    public function store(PatrocinadorRequest $request): RedirectResponse
    {
        $patrocinador = Patrocinador::create($request->validated());

        return redirect()->route('admin.patrocinadores.show', $patrocinador)->with('sucesso', 'Patrocinador cadastrado com sucesso.');
    }

    public function edit(Patrocinador $patrocinador): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.patrocinadores.edit', compact('patrocinador'));
    }

    public function update(PatrocinadorRequest $request, Patrocinador $patrocinador): RedirectResponse
    {
        // O nome (PK) não é alterado: patrocinio.nome_patrocinador não tem ON UPDATE CASCADE.
        $patrocinador->update(['observacoes' => $request->validated('observacoes')]);

        return redirect()->route('admin.patrocinadores.show', $patrocinador)->with('sucesso', 'Patrocinador atualizado.');
    }

    public function destroy(Patrocinador $patrocinador): RedirectResponse
    {
        $this->authorize('cadastros-globais');
        $patrocinador->delete(); // patrocínios vinculados -> erro 1451 tratado em ErrosBanco

        return redirect()->route('admin.patrocinadores.index')->with('sucesso', 'Patrocinador excluído.');
    }
}
