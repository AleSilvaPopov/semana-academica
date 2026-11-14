<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusConta;
use App\Enums\TipoParticipante;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ParticipanteRequest;
use App\Models\Participante;
use App\Services\ParticipanteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Cadastro global de participantes (público do evento) com telefones. */
class ParticipanteController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = Participante::query()->with('ministrante:cpf')->withCount('inscricoes');

        // nome_completo tem o índice idx_participante_nome; tipo + instituição usam idx_participante_tipo_instituicao.
        $this->buscar($query, $request->query('q'), ['nome_completo', 'email', 'cpf']);
        $query->when($request->query('tipo'), fn ($q, $v) => $q->where('tipo_participante', $v))
            ->when($request->query('instituicao'), fn ($q, $v) => $q->where('instituicao_origem', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('status_conta', $v));
        $this->ordenar($query, $request, [
            'nome' => 'nome_completo',
            'email' => 'email',
            'tipo' => 'tipo_participante',
            'instituicao' => 'instituicao_origem',
            'inscricoes' => 'inscricoes_count',
        ], 'nome');

        return view('admin.participantes.index', [
            'participantes' => $query->paginate($this->porPagina)->withQueryString(),
            'tipos' => TipoParticipante::opcoes(),
            'status' => StatusConta::opcoes(),
            'instituicoes' => Participante::query()->distinct()->orderBy('instituicao_origem')->pluck('instituicao_origem', 'instituicao_origem'),
        ]);
    }

    public function show(Participante $participante): View
    {
        $participante->load([
            'telefones',
            'ministrante.certificados',
            'inscricoes' => fn ($q) => $q->with('atividade.coordenadoria.comissao')->orderByDesc('data_inscricao'),
        ]);

        return view('admin.participantes.show', compact('participante'));
    }

    public function create(): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.participantes.create', ['participante' => null]);
    }

    public function store(ParticipanteRequest $request, ParticipanteService $servico): RedirectResponse
    {
        $participante = $servico->criar($request->validated());

        return redirect()->route('admin.participantes.show', $participante)->with('sucesso', 'Participante cadastrado com sucesso.');
    }

    public function edit(Participante $participante): View
    {
        $this->authorize('cadastros-globais');
        $participante->load('telefones');

        return view('admin.participantes.edit', compact('participante'));
    }

    public function update(ParticipanteRequest $request, Participante $participante, ParticipanteService $servico): RedirectResponse
    {
        $servico->atualizar($participante, $request->validated());

        return redirect()->route('admin.participantes.show', $participante)->with('sucesso', 'Participante atualizado com sucesso.');
    }

    public function destroy(Participante $participante): RedirectResponse
    {
        $this->authorize('cadastros-globais');
        // Telefones e ministrante saem por CASCADE; inscrições ou atividades ministradas bloqueiam (1451).
        $participante->delete();

        return redirect()->route('admin.participantes.index')->with('sucesso', 'Participante excluído.');
    }
}
