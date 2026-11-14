<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IntegranteRequest;
use App\Models\Curso;
use App\Models\Integrante;
use App\Services\EdicaoAtual;
use App\Services\IntegranteService;
use App\Support\Opcoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Integrantes da comissão organizadora (cadastro global). */
class IntegranteController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $query = Integrante::query()->with(['curso', 'coordenadoria.comissao'])->withCount('coordenadoriasQueCoordena');

        // nome_completo usa o índice idx_integrante_nome.
        $this->buscar($query, $request->query('q'), ['nome_completo', 'rga', 'cpf', 'funcao']);
        $query->when($request->query('curso'), fn ($q, $v) => $q->where('id_curso', $v))
            ->when($request->query('coordenadoria'), fn ($q, $v) => $q->where('id_coordenadoria', $v))
            ->when($request->query('papel') === 'coordenadores', fn ($q) => $q->whereHas('coordenadoriasQueCoordena', fn ($c) => $c->where('id_comissao', $edicao->id())))
            ->when($request->query('papel') === 'sem_coordenadoria', fn ($q) => $q->whereNull('id_coordenadoria'));
        $this->ordenar($query, $request, [
            'nome' => 'nome_completo',
            'rga' => 'rga',
            'ingresso' => 'data_ingresso',
            'funcao' => 'funcao',
        ], 'nome');

        return view('admin.integrantes.index', [
            'integrantes' => $query->paginate($this->porPagina)->withQueryString(),
            'cursos' => Curso::orderBy('nome_curso')->pluck('nome_curso', 'id_curso'),
            'coordenadorias' => Opcoes::coordenadoriasDaEdicao($edicao->id() ?? 0),
        ]);
    }

    public function show(Integrante $integrante): View
    {
        $integrante->load([
            'curso', 'telefones', 'emails', 'coordenadoria.comissao',
            'coordenadoriasQueCoordena' => fn ($q) => $q->with('comissao')->orderByDesc('data_inicio_coordenacao'),
            'presidencias.comissao', 'vicePresidencias.comissao',
            'tarefas' => fn ($q) => $q->orderByDesc('prazo_final'),
        ])->loadCount('tarefasCriadas');

        $presidencias = $integrante->presidencias->map(fn ($p) => ['comissao' => $p->comissao, 'papel' => 'Presidente'])
            ->concat($integrante->vicePresidencias->map(fn ($p) => ['comissao' => $p->comissao, 'papel' => 'Vice-presidente']))
            ->sortByDesc(fn ($p) => $p['comissao']->data_inicio)
            ->values();

        return view('admin.integrantes.show', compact('integrante', 'presidencias'));
    }

    public function create(): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.integrantes.create', $this->dadosFormulario(new Integrante()));
    }

    public function store(IntegranteRequest $request, IntegranteService $servico): RedirectResponse
    {
        $integrante = $servico->salvar(new Integrante(), $request->validated());

        return redirect()->route('admin.integrantes.show', $integrante)->with('sucesso', 'Integrante cadastrado com sucesso.');
    }

    public function edit(Integrante $integrante): View
    {
        $this->authorize('cadastros-globais');
        $integrante->load('telefones', 'emails');

        return view('admin.integrantes.edit', $this->dadosFormulario($integrante));
    }

    public function update(IntegranteRequest $request, Integrante $integrante, IntegranteService $servico): RedirectResponse
    {
        $servico->salvar($integrante, $request->validated());

        return redirect()->route('admin.integrantes.show', $integrante)->with('sucesso', 'Integrante atualizado com sucesso.');
    }

    public function destroy(Request $request, Integrante $integrante): RedirectResponse
    {
        $this->authorize('cadastros-globais');

        if ($integrante->is($request->user('integrante'))) {
            return back()->with('erro', 'Você não pode excluir o seu próprio cadastro.');
        }

        // Telefones e e-mails saem por CASCADE; coordenações, presidências e tarefas bloqueiam (erro 1451).
        $integrante->delete();

        return redirect()->route('admin.integrantes.index')->with('sucesso', 'Integrante excluído.');
    }

    private function dadosFormulario(Integrante $integrante): array
    {
        return [
            'integrante' => $integrante,
            'cursos' => Curso::orderBy('nome_curso')->get()->mapWithKeys(fn ($c) => [$c->id_curso => $c->nome_curso.' — '.$c->faculdade]),
            'coordenadorias' => Opcoes::coordenadoriasPorEdicao(),
        ];
    }
}
