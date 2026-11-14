<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusAtividade;
use App\Enums\TipoAtividade;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Participante;
use App\Services\EdicaoAtual;
use App\Services\InscricaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Inscrições e credenciamento (presença em lote) por atividade da edição selecionada. */
class InscricaoController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $query = Atividade::query()
            ->daEdicao($edicao->obrigatoria()->id_comissao)
            ->with('local', 'coordenadoria')
            ->withCount(['inscricoes', 'inscricoes as presentes_count' => fn ($q) => $q->where('presenca', true)]);

        $this->buscar($query, $request->query('q'), ['atividade.nome']);
        $query->when($request->query('tipo'), fn ($q, $v) => $q->where('atividade.tipo', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('atividade.status', $v));
        $this->ordenar($query, $request, [
            'data' => 'atividade.data_realizacao',
            'nome' => 'atividade.nome',
            'inscritos' => 'inscricoes_count',
            'presentes' => 'presentes_count',
        ], 'data');

        return view('admin.inscricoes.index', [
            'atividades' => $query->paginate($this->porPagina)->withQueryString(),
            'tipos' => TipoAtividade::opcoes(),
            'status' => StatusAtividade::opcoes(),
        ]);
    }

    public function show(Request $request, Atividade $atividade): View
    {
        $atividade->load('local', 'coordenadoria.comissao')->loadCount('inscricoes');

        $inscritos = $atividade->participantes()
            ->when($request->query('q'), fn ($q, $t) => $q->where(fn ($w) => $w->where('nome_completo', 'like', "%{$t}%")
                ->orWhere('participante.cpf', 'like', '%'.somente_digitos($t).'%')))
            ->when($request->query('presenca') !== null && $request->query('presenca') !== '', fn ($q) => $q->wherePivot('presenca', (bool) $request->query('presenca')))
            ->orderBy('nome_completo') // índice idx_participante_nome
            ->get();

        $disponiveis = Participante::query()
            ->where('status_conta', 'ativo')
            ->whereDoesntHave('inscricoes', fn ($q) => $q->where('id_atividade', $atividade->id_atividade))
            ->orderBy('nome_completo')
            ->get(['cpf', 'nome_completo'])
            ->mapWithKeys(fn ($p) => [$p->cpf => $p->nome_completo.' ('.formata_cpf($p->cpf).')']);

        return view('admin.inscricoes.show', [
            'atividade' => $atividade,
            'inscritos' => $inscritos,
            'disponiveis' => $disponiveis,
            'presentes' => $atividade->inscricoes()->where('presenca', true)->count(),
        ]);
    }

    /** Inscrição manual pelo painel (mesmas regras do portal). */
    public function store(Request $request, Atividade $atividade, InscricaoService $servico): RedirectResponse
    {
        $this->authorize('gerenciarInscricoes', $atividade);
        $dados = $request->validate(['cpf' => ['required', 'digits:11', Rule::exists('participante', 'cpf')]], [
            'cpf.required' => 'Escolha o participante.',
        ]);

        $participante = Participante::findOrFail($dados['cpf']);
        $servico->inscrever($participante, $atividade);

        return back()->with('sucesso', $participante->nome_completo.' foi inscrito(a).');
    }

    /** Credenciamento: grava a presença de todos os inscritos de uma vez. */
    public function presenca(Request $request, Atividade $atividade, InscricaoService $servico): RedirectResponse
    {
        $this->authorize('gerenciarInscricoes', $atividade);
        $dados = $request->validate([
            'presentes' => ['array'],
            'presentes.*' => ['digits:11'],
        ]);

        $total = $servico->registrarPresencas($atividade, $dados['presentes'] ?? []);

        return back()->with('sucesso', "Presenças registradas: {$total} participante(s) presente(s).");
    }

    public function destroy(Atividade $atividade, Participante $participante, InscricaoService $servico): RedirectResponse
    {
        $this->authorize('gerenciarInscricoes', $atividade);
        $servico->remover($participante->cpf, $atividade);

        return back()->with('sucesso', 'Inscrição de '.$participante->nome_completo.' removida.');
    }
}
