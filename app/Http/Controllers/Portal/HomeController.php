<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusAtividade;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Comissao;
use Illuminate\View\View;

/** Página inicial pública: edição mais recente, com datas, local, números e patrocinadores. */
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

        return view('portal.home', [
            'edicao' => $edicao,
            'destaques' => $atividades->take(6),
            'totais' => [
                'atividades' => $atividades->count(),
                'ministrantes' => $atividades->flatMap->ministrantes->unique('cpf')->count(),
                'vagas' => $atividades->sum('vagas'),
                'dias' => $edicao->data_inicio->diffInDays($edicao->data_termino) + 1,
            ],
        ]);
    }
}
