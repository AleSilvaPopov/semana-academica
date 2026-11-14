<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoLocal;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocalRequest;
use App\Models\LocalEvento;
use App\Services\Multivalorado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Cadastro global de locais (local_evento) com equipamentos (atributo multivalorado). */
class LocalController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = LocalEvento::query()->withCount(['equipamentos', 'atividades']);
        $this->buscar($query, $request->query('q'), ['faculdade', 'bloco', 'numero', 'observacoes']);
        $query->when($request->query('tipo'), fn ($q, $t) => $q->where('tipo', $t))
            ->when($request->query('faculdade'), fn ($q, $f) => $q->where('faculdade', $f));
        $this->ordenar($query, $request, [
            'local' => 'numero',
            'faculdade' => 'faculdade',
            'tipo' => 'tipo',
            'capacidade' => 'capacidade',
            'atividades' => 'atividades_count',
        ], 'faculdade');

        return view('admin.locais.index', [
            'locais' => $query->paginate($this->porPagina)->withQueryString(),
            'tipos' => TipoLocal::opcoes(),
            'faculdades' => LocalEvento::query()->distinct()->orderBy('faculdade')->pluck('faculdade', 'faculdade'),
        ]);
    }

    public function show(LocalEvento $local): View
    {
        $local->load([
            'equipamentos',
            'atividades' => fn ($q) => $q->with('coordenadoria.comissao')->withCount('inscricoes')->orderByDesc('data_realizacao'),
        ]);

        return view('admin.locais.show', compact('local'));
    }

    public function create(): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.locais.create', $this->dadosFormulario(new LocalEvento()));
    }

    public function store(LocalRequest $request): RedirectResponse
    {
        $local = DB::transaction(function () use ($request) {
            $local = LocalEvento::criarComNovoId($request->safe()->except('equipamentos'));
            Multivalorado::sincronizar($local->equipamentos(), 'equipamento', $request->validated('equipamentos', []));

            return $local;
        });

        return redirect()->route('admin.locais.show', $local)->with('sucesso', 'Local cadastrado com sucesso.');
    }

    public function edit(LocalEvento $local): View
    {
        $this->authorize('cadastros-globais');
        $local->load('equipamentos');

        return view('admin.locais.edit', $this->dadosFormulario($local));
    }

    public function update(LocalRequest $request, LocalEvento $local): RedirectResponse
    {
        DB::transaction(function () use ($request, $local) {
            $local->update($request->safe()->except('equipamentos'));
            Multivalorado::sincronizar($local->equipamentos(), 'equipamento', $request->validated('equipamentos', []));
        });

        return redirect()->route('admin.locais.show', $local)->with('sucesso', 'Local atualizado com sucesso.');
    }

    public function destroy(LocalEvento $local): RedirectResponse
    {
        $this->authorize('cadastros-globais');
        // Equipamentos saem por ON DELETE CASCADE; atividades no local bloqueiam (erro 1451).
        $local->delete();

        return redirect()->route('admin.locais.index')->with('sucesso', 'Local excluído.');
    }

    private function dadosFormulario(LocalEvento $local): array
    {
        return [
            'local' => $local,
            'tipos' => TipoLocal::opcoes(),
            'faculdades' => LocalEvento::query()->distinct()->orderBy('faculdade')->pluck('faculdade'),
        ];
    }
}
