<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CoordenadoriaRequest;
use App\Models\Coordenadoria;
use App\Services\EdicaoAtual;
use App\Support\Opcoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Coordenadorias da edição selecionada. */
class CoordenadoriaController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $idComissao = $edicao->obrigatoria()->id_comissao;

        // A quantidade de integrantes vem da VIEW vw_quantidade_integrantes (atributo derivado).
        $query = Coordenadoria::query()
            ->daEdicao($idComissao)
            ->leftJoin('vw_quantidade_integrantes as vw', 'vw.id_coordenadoria', '=', 'coordenadoria.id_coordenadoria')
            ->select('coordenadoria.*', 'vw.quantidade_integrantes')
            ->with('coordenador')
            ->withCount('atividades');

        $this->buscar($query, $request->query('q'), ['coordenadoria.nome', 'coordenadoria.descricao_atuacao']);
        $query->when($request->query('situacao') === 'ativa', fn ($q) => $q->where(fn ($w) => $w->whereNull('data_termino_coordenacao')->orWhereDate('data_termino_coordenacao', '>=', today())))
            ->when($request->query('situacao') === 'encerrada', fn ($q) => $q->whereDate('data_termino_coordenacao', '<', today()));
        $this->ordenar($query, $request, [
            'nome' => 'coordenadoria.nome',
            'integrantes' => 'vw.quantidade_integrantes',
            'atividades' => 'atividades_count',
            'inicio' => 'coordenadoria.data_inicio_coordenacao',
        ], 'nome');

        return view('admin.coordenadorias.index', [
            'coordenadorias' => $query->paginate($this->porPagina)->withQueryString(),
        ]);
    }

    public function show(Coordenadoria $coordenadoria): View
    {
        $coordenadoria->load([
            'comissao', 'coordenador', 'quantidade',
            'membros' => fn ($q) => $q->orderBy('nome_completo')->with('curso'),
            'atividades' => fn ($q) => $q->with('local')->withCount('inscricoes')->orderBy('data_realizacao'),
        ]);
        $tarefas = $coordenadoria->tarefas()->with('responsaveis')->orderBy('prazo_final')->get();

        return view('admin.coordenadorias.show', compact('coordenadoria', 'tarefas'));
    }

    public function create(EdicaoAtual $edicao): View
    {
        $this->authorize('create', Coordenadoria::class);

        return view('admin.coordenadorias.create', [
            'coordenadoria' => new Coordenadoria(['data_inicio_coordenacao' => today()]),
            'integrantes' => Opcoes::integrantes(),
            'edicao' => $edicao->obrigatoria(),
        ]);
    }

    public function store(CoordenadoriaRequest $request, EdicaoAtual $edicao): RedirectResponse
    {
        $coordenadoria = Coordenadoria::criarComNovoId($request->validated() + ['id_comissao' => $edicao->obrigatoria()->id_comissao]);

        return redirect()->route('admin.coordenadorias.show', $coordenadoria)->with('sucesso', 'Coordenadoria cadastrada com sucesso.');
    }

    public function edit(Coordenadoria $coordenadoria): View
    {
        $this->authorize('update', $coordenadoria);

        return view('admin.coordenadorias.edit', [
            'coordenadoria' => $coordenadoria,
            'integrantes' => Opcoes::integrantes(),
            'edicao' => $coordenadoria->comissao,
        ]);
    }

    public function update(CoordenadoriaRequest $request, Coordenadoria $coordenadoria): RedirectResponse
    {
        $coordenadoria->update($request->validated());

        return redirect()->route('admin.coordenadorias.show', $coordenadoria)->with('sucesso', 'Coordenadoria atualizada com sucesso.');
    }

    public function destroy(Coordenadoria $coordenadoria): RedirectResponse
    {
        $this->authorize('delete', $coordenadoria);

        // Tarefas não têm FK para coordenadoria, mas pertencem a ela pela data de criação:
        // excluir deixaria o criador sem coordenação e o trigger bloquearia edições futuras.
        $tarefas = $coordenadoria->tarefas()->count();
        if ($tarefas > 0) {
            return back()->with('erro', "Não é possível excluir: existem {$tarefas} tarefas criadas nesta coordenação.");
        }

        // Membros (integrante.id_coordenadoria) e atividades vinculadas geram erro 1451 -> ErrosBanco.
        $coordenadoria->delete();

        return redirect()->route('admin.coordenadorias.index')->with('sucesso', 'Coordenadoria excluída.');
    }
}
