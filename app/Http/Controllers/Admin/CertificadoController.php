<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Participante;
use App\Services\CertificadoService;
use App\Services\EdicaoAtual;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Certificados: participantes com presenca = 1, por atividade da edição selecionada. */
class CertificadoController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $query = Atividade::query()
            ->daEdicao($edicao->obrigatoria()->id_comissao)
            ->with('coordenadoria')
            ->withCount(['inscricoes', 'inscricoes as presentes_count' => fn ($q) => $q->where('presenca', true)]);

        $this->buscar($query, $request->query('q'), ['atividade.nome']);
        $query->when($request->query('situacao') === 'com', fn ($q) => $q->whereHas('inscricoes', fn ($i) => $i->where('presenca', true)));
        $this->ordenar($query, $request, ['data' => 'atividade.data_realizacao', 'nome' => 'atividade.nome', 'presentes' => 'presentes_count'], 'data');

        return view('admin.certificados.index', ['atividades' => $query->paginate($this->porPagina)->withQueryString()]);
    }

    public function show(Request $request, Atividade $atividade): View
    {
        $atividade->load('coordenadoria.comissao');

        $presentes = $atividade->participantes()
            ->wherePivot('presenca', true)
            ->when($request->query('q'), fn ($q, $t) => $q->where('nome_completo', 'like', "%{$t}%"))
            ->orderBy('nome_completo')
            ->get();

        return view('admin.certificados.show', compact('atividade', 'presentes'));
    }

    public function emitir(Atividade $atividade, Participante $participante, CertificadoService $servico): View
    {
        return view('certificado', $servico->dados($participante, $atividade) + ['voltar' => route('admin.certificados.show', $atividade)]);
    }
}
