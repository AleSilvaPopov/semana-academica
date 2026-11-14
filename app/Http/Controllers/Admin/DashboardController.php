<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusAtividade;
use App\Enums\StatusTarefa;
use App\Enums\TipoPatrocinio;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Inscricao;
use App\Models\Patrocinio;
use App\Models\Tarefa;
use App\Services\EdicaoAtual;
use Illuminate\View\View;

/** Indicadores da edição selecionada. */
class DashboardController extends Controller
{
    public function __invoke(EdicaoAtual $edicaoAtual): View
    {
        $edicao = $edicaoAtual->obrigatoria();
        $id = $edicao->id_comissao;

        $atividades = Atividade::daEdicao($id)
            ->with('local', 'coordenadoria')
            ->withCount(['inscricoes', 'inscricoes as presentes_count' => fn ($q) => $q->where('presenca', true)])
            ->orderBy('data_realizacao')
            ->get();

        $porStatus = collect(StatusAtividade::cases())->mapWithKeys(fn ($s) => [$s->value => $atividades->where('status', $s)->count()]);

        $idsAtividades = $atividades->pluck('id_atividade');
        $inscricoes = Inscricao::whereIn('id_atividade', $idsAtividades);
        $totalInscricoes = (clone $inscricoes)->count();
        $participantesUnicos = (clone $inscricoes)->distinct('cpf')->count('cpf');

        // Taxa de presença considera só atividades já realizadas (as futuras ainda não têm presença).
        $realizadas = $atividades->where('status', StatusAtividade::Realizada);
        $inscritosRealizadas = $realizadas->sum('inscricoes_count');
        $taxaPresenca = $inscritosRealizadas > 0 ? round($realizadas->sum('presentes_count') / $inscritosRealizadas * 100, 1) : null;

        $tarefas = Tarefa::daEdicao($id);
        $tarefasPorStatus = (clone $tarefas)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $tarefasAtrasadas = (clone $tarefas)->atrasadas()->with('criador', 'responsaveis')->orderBy('prazo_final')->get();

        $patrocinios = Patrocinio::where('id_comissao', $id)
            ->selectRaw('tipo_patrocinio, COUNT(*) as quantidade, COALESCE(SUM(valor), 0) as soma')
            ->groupBy('tipo_patrocinio')->get()->keyBy(fn ($l) => $l->tipo_patrocinio->value);

        $proximas = $atividades->filter(fn ($a) => $a->data_realizacao->isFuture() && $a->status !== StatusAtividade::Cancelada)->take(5);

        return view('admin.dashboard', [
            'edicao' => $edicao,
            'atividades' => $atividades,
            'porStatus' => $porStatus,
            'totalInscricoes' => $totalInscricoes,
            'participantesUnicos' => $participantesUnicos,
            'taxaPresenca' => $taxaPresenca,
            'vagasTotais' => $atividades->where('status', '!=', StatusAtividade::Cancelada)->sum('vagas'),
            'tarefasPorStatus' => $tarefasPorStatus,
            'tarefasAbertas' => collect(StatusTarefa::abertos())->sum(fn ($s) => $tarefasPorStatus[$s] ?? 0),
            'tarefasAtrasadas' => $tarefasAtrasadas,
            'patrocinios' => $patrocinios,
            'totalFinanceiro' => $patrocinios->sum('soma'),
            'tiposPatrocinio' => TipoPatrocinio::cases(),
            'proximas' => $proximas,
        ]);
    }
}
