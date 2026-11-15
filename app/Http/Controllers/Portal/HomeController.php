<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusAtividade;
use App\Enums\TipoAtividade;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Comissao;
use Illuminate\View\View;

/**
 * Página inicial pública (landing da edição mais recente):
 * topo com datas e local, maratona (config), palestras, minicursos,
 * programação por dia, mapa, patrocinadores e chamada para inscrição.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $edicao = Comissao::maisRecente();

        if (! $edicao) {
            return view('portal.home', ['edicao' => null]);
        }

        $edicao->load(['patrocinios' => fn ($q) => $q->with('patrocinador')->orderByRaw('valor IS NULL')->orderByDesc('valor')]);

        $atividades = Atividade::daEdicao($edicao->id_comissao)
            ->where('status', '!=', StatusAtividade::Cancelada->value)
            ->with('local', 'ministrantes.participante')
            ->withCount('inscricoes')
            ->orderBy('data_realizacao')
            ->get();

        // Pontos do mapa: os do config ou "Credenciamento" + os locais usados na edição.
        $pontos = config('identidade.mapa.pontos')
            ?? collect(['Credenciamento'])
                ->merge($atividades->pluck('local')->filter()->unique('id_local')->map(
                    fn ($l) => $l->nome_curto.' — '.mb_strtolower($l->tipo?->rotulo() ?? '')
                ))
                ->take(4)
                ->values()
                ->all();

        return view('portal.home', [
            'edicao' => $edicao,
            'palestras' => $atividades->where('tipo', TipoAtividade::Palestra)->take(3)->values(),
            'minicursos' => $atividades->where('tipo', '!=', TipoAtividade::Palestra)->take(3)->values(),
            'dias' => $atividades->groupBy(fn ($a) => $a->data_realizacao->toDateString()),
            'pontos' => $pontos,
            'endereco' => config('identidade.mapa.endereco') ?? $edicao->local_sede,
        ]);
    }
}
