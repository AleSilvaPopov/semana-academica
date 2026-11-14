<?php

namespace App\Http\Controllers\Portal;

use App\Enums\TipoAtividade;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Comissao;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Programação pública (grade por dia) e detalhes da atividade. */
class ProgramacaoController extends Controller
{
    public function index(Request $request): View
    {
        $edicoes = Comissao::query()->recentesPrimeiro()->get();
        $edicao = $edicoes->firstWhere('id_comissao', (int) $request->query('edicao')) ?? $edicoes->first();

        $atividades = collect();
        if ($edicao) {
            // data_realizacao usa o índice idx_atividade_data; tipo usa idx_atividade_tipo_status.
            $atividades = Atividade::daEdicao($edicao->id_comissao)
                ->when($request->query('tipo'), fn ($q, $t) => $q->where('tipo', $t))
                ->with(['local', 'ministrantes.participante'])
                ->withCount('inscricoes')
                ->orderBy('data_realizacao')
                ->get();
        }

        return view('portal.programacao', [
            'edicoes' => $edicoes,
            'edicao' => $edicao,
            'dias' => $atividades->groupBy(fn ($a) => $a->data_realizacao->toDateString()),
            'tipos' => TipoAtividade::opcoes(),
            'minhas' => $this->inscricoesDoParticipante(),
        ]);
    }

    public function show(Atividade $atividade): View
    {
        $atividade->load(['local.equipamentos', 'coordenadoria.comissao', 'ministrantes.participante', 'ministrantes.certificados'])
            ->loadCount('inscricoes');

        $participante = auth('participante')->user();
        $inscricao = $participante ? Inscricao::daChave($participante->cpf, $atividade->id_atividade)->first() : null;

        return view('portal.atividade', compact('atividade', 'inscricao'));
    }

    /** IDs das atividades em que o participante logado está inscrito (para marcar na grade). */
    private function inscricoesDoParticipante(): array
    {
        $participante = auth('participante')->user();

        return $participante ? Inscricao::where('cpf', $participante->cpf)->pluck('id_atividade')->all() : [];
    }
}
