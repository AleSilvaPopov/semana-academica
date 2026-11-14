<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusTarefa;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TarefaRequest;
use App\Models\Integrante;
use App\Models\Tarefa;
use App\Services\EdicaoAtual;
use App\Services\Permissoes;
use App\Support\Opcoes;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tarefas da edição selecionada (responsáveis = N:N tarefa_responsavel, via sync) e
 * "Minhas tarefas" do integrante logado.
 */
class TarefaController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $idComissao = $edicao->obrigatoria()->id_comissao;
        $hoje = today()->toDateString();

        $query = Tarefa::query()->daEdicao($idComissao)->with(['criador', 'responsaveis']);

        $this->buscar($query, $request->query('q'), ['tarefa.descricao']);
        // status + prazo_final usam o índice idx_tarefa_status_prazo.
        $query->when($request->query('status'), fn ($q, $v) => $q->where('tarefa.status', $v))
            ->when($request->query('criador'), fn ($q, $v) => $q->where('tarefa.rga_criador', $v))
            ->when($request->query('prazo') === 'atrasadas', fn ($q) => $q->atrasadas())
            ->when($request->query('prazo') === 'semana', fn ($q) => $q->whereIn('tarefa.status', StatusTarefa::abertos())
                ->whereBetween('tarefa.prazo_final', [$hoje, today()->addDays(7)->toDateString()]))
            ->when($request->query('prazo') === 'em_dia', fn ($q) => $q->where(fn ($w) => $w->where('tarefa.prazo_final', '>=', $hoje)
                ->orWhereNotIn('tarefa.status', StatusTarefa::abertos())));
        $this->ordenar($query, $request, [
            'prazo' => 'tarefa.prazo_final',
            'criacao' => 'tarefa.data_criacao',
            'status' => 'tarefa.status',
            'id' => 'tarefa.id_tarefa',
        ], 'prazo');

        return view('admin.tarefas.index', [
            'tarefas' => $query->paginate($this->porPagina)->withQueryString(),
            'statusOpcoes' => StatusTarefa::opcoes(),
            'criadores' => Integrante::whereIn('rga', Tarefa::daEdicao($idComissao)->select('rga_criador'))->orderBy('nome_completo')->pluck('nome_completo', 'rga'),
            'atrasadas' => Tarefa::daEdicao($idComissao)->atrasadas()->count(),
        ]);
    }

    public function minhas(Request $request): View
    {
        $usuario = $request->user('integrante');

        $tarefas = $usuario->tarefas()
            ->with('criador')
            ->when($request->query('status'), fn ($q, $v) => $q->where('tarefa.status', $v))
            ->orderByRaw("FIELD(tarefa.status, 'em_andamento', 'pendente', 'concluida', 'cancelada')")
            ->orderBy('tarefa.prazo_final')
            ->paginate($this->porPagina)
            ->withQueryString();

        return view('admin.tarefas.minhas', [
            'tarefas' => $tarefas,
            'statusOpcoes' => StatusTarefa::opcoes(),
            'resumo' => $usuario->tarefas()->selectRaw('tarefa.status, COUNT(*) as total')->groupBy('tarefa.status')->pluck('total', 'status'),
        ]);
    }

    public function show(Tarefa $tarefa): View
    {
        $tarefa->load('criador', 'responsaveis.coordenadoria');

        return view('admin.tarefas.show', ['tarefa' => $tarefa, 'coordenadoria' => $tarefa->coordenadoriaVigente()]);
    }

    public function create(Request $request, EdicaoAtual $edicao): View
    {
        $this->authorize('create', Tarefa::class);

        $tarefa = new Tarefa(['data_criacao' => today(), 'prazo_final' => today()->addDays(14), 'status' => StatusTarefa::Pendente]);

        return view('admin.tarefas.create', $this->dadosFormulario($request, $tarefa, $edicao->obrigatoria()->id_comissao));
    }

    public function store(TarefaRequest $request): RedirectResponse
    {
        try {
            $tarefa = DB::transaction(function () use ($request) {
                $tarefa = Tarefa::criarComNovoId($request->safe()->except('responsaveis'));
                $tarefa->responsaveis()->sync($request->validated('responsaveis', []));

                return $tarefa;
            });
        } catch (QueryException $e) {
            return $this->erroDoTrigger($e);
        }

        return redirect()->route('admin.tarefas.show', $tarefa)->with('sucesso', 'Tarefa criada com sucesso.');
    }

    public function edit(Request $request, Tarefa $tarefa): View
    {
        $this->authorize('update', $tarefa);
        $tarefa->load('responsaveis');
        $idComissao = $tarefa->coordenadoriaVigente()?->id_comissao ?? app(EdicaoAtual::class)->id();

        return view('admin.tarefas.edit', $this->dadosFormulario($request, $tarefa, (int) $idComissao));
    }

    public function update(TarefaRequest $request, Tarefa $tarefa): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $tarefa) {
                $tarefa->update($request->safe()->except('responsaveis'));
                $tarefa->responsaveis()->sync($request->validated('responsaveis', []));
            });
        } catch (QueryException $e) {
            return $this->erroDoTrigger($e);
        }

        return redirect()->route('admin.tarefas.show', $tarefa)->with('sucesso', 'Tarefa atualizada com sucesso.');
    }

    /** Responsável (ou criador/presidência) muda só o status. */
    public function alterarStatus(Request $request, Tarefa $tarefa): RedirectResponse
    {
        $this->authorize('alterarStatus', $tarefa);
        $dados = $request->validate(['status' => ['required', Rule::enum(StatusTarefa::class)]]);

        try {
            $tarefa->update(['status' => $dados['status']]); // dispara trg_tarefa_criador_bu
        } catch (QueryException $e) {
            return $this->erroDoTrigger($e);
        }

        return back()->with('sucesso', 'Status da tarefa alterado para "'.StatusTarefa::from($dados['status'])->rotulo().'".');
    }

    public function destroy(Tarefa $tarefa): RedirectResponse
    {
        $this->authorize('delete', $tarefa);
        $tarefa->delete(); // tarefa_responsavel sai por ON DELETE CASCADE

        return redirect()->route('admin.tarefas.index')->with('sucesso', 'Tarefa excluída.');
    }

    /**
     * Captura o SIGNAL SQLSTATE '45000' dos triggers trg_tarefa_criador_bi/bu e mostra a
     * mensagem do próprio trigger ("Somente coordenadores podem criar tarefas.") no formulário.
     */
    private function erroDoTrigger(QueryException $e): RedirectResponse
    {
        if (($e->errorInfo[0] ?? null) === '45000') {
            return back()->withInput()->withErrors(['rga_criador' => $e->errorInfo[2] ?? 'Somente coordenadores podem criar tarefas.']);
        }

        throw $e;
    }

    private function dadosFormulario(Request $request, Tarefa $tarefa, int $idComissao): array
    {
        $usuario = $request->user('integrante');
        $criadores = TarefaRequest::criadoresPermitidos($usuario, $idComissao);

        return [
            'tarefa' => $tarefa,
            'statusOpcoes' => StatusTarefa::opcoes(),
            // Só coordenadores aparecem como criador (com a coordenadoria entre parênteses).
            'criadores' => Integrante::whereIn('rga', $criadores)
                ->with(['coordenadoriasQueCoordena' => fn ($q) => $q->where('id_comissao', $idComissao)])
                ->orderBy('nome_completo')->get()
                ->mapWithKeys(fn ($i) => [$i->rga => $i->nome_completo.' — '.$i->coordenadoriasQueCoordena->pluck('nome')->join(', ')]),
            'criadorFixo' => ! app(Permissoes::class)->presideEdicao($usuario, $idComissao),
            'integrantes' => Opcoes::integrantes(),
        ];
    }
}
