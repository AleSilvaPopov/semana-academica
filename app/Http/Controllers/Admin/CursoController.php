<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CursoRequest;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Cadastro global de cursos (presidente/vice de qualquer edição). */
class CursoController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = Curso::query()->withCount('integrantes');
        $this->buscar($query, $request->query('q'), ['nome_curso', 'faculdade']);
        $query->when($request->query('faculdade'), fn ($q, $f) => $q->where('faculdade', $f));
        $this->ordenar($query, $request, [
            'nome' => 'nome_curso',
            'faculdade' => 'faculdade',
            'integrantes' => 'integrantes_count',
        ], 'nome');

        return view('admin.cursos.index', [
            'cursos' => $query->paginate($this->porPagina)->withQueryString(),
            'faculdades' => Curso::query()->distinct()->orderBy('faculdade')->pluck('faculdade', 'faculdade'),
        ]);
    }

    public function show(Curso $curso): View
    {
        $curso->load(['integrantes' => fn ($q) => $q->orderBy('nome_completo')->with('coordenadoria.comissao')]);

        return view('admin.cursos.show', compact('curso'));
    }

    public function create(): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.cursos.create', ['curso' => new Curso(), 'faculdades' => $this->faculdades()]);
    }

    public function store(CursoRequest $request): RedirectResponse
    {
        $curso = Curso::criarComNovoId($request->validated());

        return redirect()->route('admin.cursos.show', $curso)->with('sucesso', 'Curso cadastrado com sucesso.');
    }

    public function edit(Curso $curso): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.cursos.edit', ['curso' => $curso, 'faculdades' => $this->faculdades()]);
    }

    public function update(CursoRequest $request, Curso $curso): RedirectResponse
    {
        $curso->update($request->validated());

        return redirect()->route('admin.cursos.show', $curso)->with('sucesso', 'Curso atualizado com sucesso.');
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        $this->authorize('cadastros-globais');

        // Integrantes vinculados geram erro 1451, convertido em mensagem pelo App\Exceptions\ErrosBanco.
        $curso->delete();

        return redirect()->route('admin.cursos.index')->with('sucesso', 'Curso excluído.');
    }

    private function faculdades()
    {
        return Curso::query()->distinct()->orderBy('faculdade')->pluck('faculdade');
    }
}
