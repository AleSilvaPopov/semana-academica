<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ComissaoRequest;
use App\Models\Comissao;
use App\Services\EdicaoAtual;
use App\Services\PresidenciaService;
use App\Support\Opcoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Edições do evento (comissao_organizadora) com presidente e vice (presidencia). */
class ComissaoController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = Comissao::query()
            ->with('presidencia.presidente', 'presidencia.vice')
            ->withCount(['coordenadorias', 'atividades', 'patrocinios']);

        $this->buscar($query, $request->query('q'), ['nome_evento', 'local_sede', 'observacoes']);
        $query->when($request->query('ano'), fn ($q, $ano) => $q->whereYear('data_inicio', $ano));
        $this->ordenar($query, $request, [
            'edicao' => 'edicao',
            'inicio' => 'data_inicio',
            'local' => 'local_sede',
            'atividades' => 'atividades_count',
        ], 'inicio', 'desc');

        return view('admin.comissoes.index', [
            'comissoes' => $query->paginate($this->porPagina)->withQueryString(),
            'anos' => Comissao::query()->orderByDesc('data_inicio')->pluck('data_inicio')->mapWithKeys(fn ($d) => [$d->year => $d->year]),
        ]);
    }

    public function show(Comissao $comissao): View
    {
        $comissao->load([
            'presidencia.presidente', 'presidencia.vice',
            'coordenadorias' => fn ($q) => $q->with(['coordenador', 'quantidade'])->withCount('atividades')->orderBy('nome'),
            'atividades' => fn ($q) => $q->with('local', 'coordenadoria')->withCount('inscricoes')->orderBy('data_realizacao'),
            'patrocinios' => fn ($q) => $q->with('patrocinador')->orderByDesc('valor'),
        ]);

        return view('admin.comissoes.show', compact('comissao'));
    }

    public function create(): View
    {
        $this->authorize('create', Comissao::class);
        $ultima = Comissao::maisRecente();

        $nova = new Comissao([
            'nome_evento' => $ultima?->nome_evento ?? 'Semana Acadêmica de Computação da UFGD',
            'edicao' => ($ultima?->edicao ?? 0) + 1,
            'local_sede' => $ultima?->local_sede,
        ]);

        return view('admin.comissoes.create', ['comissao' => $nova, 'integrantes' => Opcoes::integrantes()]);
    }

    public function store(ComissaoRequest $request, PresidenciaService $servico, EdicaoAtual $edicao): RedirectResponse
    {
        $comissao = $servico->criarEdicao($request->validated());
        $edicao->selecionar($comissao->id_comissao);

        return redirect()->route('admin.comissoes.show', $comissao)
            ->with('sucesso', 'Edição criada e selecionada. Cadastre agora as coordenadorias.');
    }

    public function edit(Comissao $comissao): View
    {
        $this->authorize('update', $comissao);
        $comissao->load('presidencia');

        return view('admin.comissoes.edit', ['comissao' => $comissao, 'integrantes' => Opcoes::integrantes()]);
    }

    public function update(ComissaoRequest $request, Comissao $comissao, PresidenciaService $servico): RedirectResponse
    {
        $servico->atualizarEdicao($comissao, $request->validated());

        return redirect()->route('admin.comissoes.show', $comissao)->with('sucesso', 'Edição atualizada com sucesso.');
    }

    public function destroy(Comissao $comissao, PresidenciaService $servico, EdicaoAtual $edicao): RedirectResponse
    {
        $this->authorize('delete', $comissao);
        $servico->excluirEdicao($comissao);

        if ($edicao->id() === $comissao->id_comissao) {
            session()->forget('admin.edicao_id');
        }

        return redirect()->route('admin.comissoes.index')->with('sucesso', 'Edição excluída.');
    }
}
