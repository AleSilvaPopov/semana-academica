<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusAtividade;
use App\Enums\TipoAtividade;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AtividadeRequest;
use App\Models\Atividade;
use App\Models\Coordenadoria;
use App\Models\LocalEvento;
use App\Models\Ministrante;
use App\Services\EdicaoAtual;
use App\Support\Opcoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Atividades da edição selecionada, com ministrantes (N:N ministra, via sync). */
class AtividadeController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $idComissao = $edicao->obrigatoria()->id_comissao;

        $query = Atividade::query()
            ->daEdicao($idComissao)
            ->with(['local', 'coordenadoria', 'ministrantes.participante'])
            ->withCount('inscricoes');

        $this->buscar($query, $request->query('q'), ['atividade.nome', 'atividade.descricao']);
        // tipo + status usam o índice idx_atividade_tipo_status; dia usa idx_atividade_data.
        $query->when($request->query('tipo'), fn ($q, $v) => $q->where('atividade.tipo', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('atividade.status', $v))
            ->when($request->query('coordenadoria'), fn ($q, $v) => $q->where('atividade.id_coordenadoria', $v))
            ->when($request->query('dia'), fn ($q, $v) => $q->whereDate('atividade.data_realizacao', $v));
        $this->ordenar($query, $request, [
            'data' => 'atividade.data_realizacao',
            'nome' => 'atividade.nome',
            'vagas' => 'atividade.vagas',
            'inscritos' => 'inscricoes_count',
            'valor' => 'atividade.valor_inscricao',
        ], 'data');

        return view('admin.atividades.index', [
            'atividades' => $query->paginate($this->porPagina)->withQueryString(),
            'tipos' => TipoAtividade::opcoes(),
            'status' => StatusAtividade::opcoes(),
            'coordenadorias' => Opcoes::coordenadoriasDaEdicao($idComissao),
            'dias' => Atividade::daEdicao($idComissao)->selectRaw('DATE(data_realizacao) as dia')->distinct()->orderBy('dia')->pluck('dia')
                ->mapWithKeys(fn ($d) => [$d => formata_data($d)]),
        ]);
    }

    public function show(Atividade $atividade): View
    {
        $atividade->load([
            'local.equipamentos', 'coordenadoria.comissao', 'coordenadoria.coordenador',
            'ministrantes.participante', 'ministrantes.certificados',
        ])->loadCount(['inscricoes', 'inscricoes as presentes_count' => fn ($q) => $q->where('presenca', true)]);

        return view('admin.atividades.show', compact('atividade'));
    }

    public function create(Request $request, EdicaoAtual $edicao): View
    {
        $this->authorize('create', Atividade::class);
        $idComissao = $edicao->obrigatoria()->id_comissao;

        $atividade = new Atividade([
            'status' => StatusAtividade::Planejada,
            'valor_inscricao' => 0,
            'duracao_minutos' => 60,
            'data_realizacao' => $edicao->get()->data_inicio->copy()->setTime(19, 0),
        ]);

        return view('admin.atividades.create', $this->dadosFormulario($atividade, $idComissao, $request));
    }

    public function store(AtividadeRequest $request): RedirectResponse
    {
        $atividade = DB::transaction(function () use ($request) {
            $atividade = Atividade::criarComNovoId($request->safe()->except('ministrantes'));
            $atividade->ministrantes()->sync($request->validated('ministrantes', []));

            return $atividade;
        });

        return redirect()->route('admin.atividades.show', $atividade)->with('sucesso', 'Atividade cadastrada com sucesso.');
    }

    public function edit(Request $request, Atividade $atividade): View
    {
        $this->authorize('update', $atividade);
        $atividade->load('ministrantes', 'coordenadoria');

        return view('admin.atividades.edit', $this->dadosFormulario($atividade, $atividade->coordenadoria->id_comissao, $request));
    }

    public function update(AtividadeRequest $request, Atividade $atividade): RedirectResponse
    {
        DB::transaction(function () use ($request, $atividade) {
            $atividade->update($request->safe()->except('ministrantes'));
            // N:N sem atributos: sync() insere os novos e apaga os removidos da tabela ministra.
            $atividade->ministrantes()->sync($request->validated('ministrantes', []));
        });

        return redirect()->route('admin.atividades.show', $atividade)->with('sucesso', 'Atividade atualizada com sucesso.');
    }

    public function destroy(Atividade $atividade): RedirectResponse
    {
        $this->authorize('delete', $atividade);

        // inscricao e ministra têm ON DELETE CASCADE: para não perder presenças/certificados,
        // o sistema só exclui atividade sem inscrições (cancele-a mudando o status).
        $inscritos = $atividade->inscricoes()->count();
        if ($inscritos > 0) {
            return back()->with('erro', "Não é possível excluir: existem {$inscritos} inscrições vinculadas. Se a atividade não vai acontecer, mude o status para \"Cancelada\".");
        }

        $atividade->delete();

        return redirect()->route('admin.atividades.index')->with('sucesso', 'Atividade excluída.');
    }

    private function dadosFormulario(Atividade $atividade, int $idComissao, Request $request): array
    {
        $permitidas = AtividadeRequest::coordenadoriasDoUsuario($request->user('integrante'), $idComissao);

        return [
            'atividade' => $atividade,
            'edicao' => \App\Models\Comissao::find($idComissao),
            'tipos' => TipoAtividade::opcoes(),
            'status' => StatusAtividade::opcoes(),
            'coordenadorias' => Coordenadoria::whereIn('id_coordenadoria', $permitidas)->orderBy('nome')->pluck('nome', 'id_coordenadoria'),
            'locais' => LocalEvento::orderBy('bloco')->orderBy('numero')->get()->mapWithKeys(fn ($l) => [$l->id_local => $l->rotulo]),
            'ministrantes' => Ministrante::with('participante:cpf,nome_completo')->get()
                ->mapWithKeys(fn ($m) => [$m->cpf => $m->participante->nome_completo])->sort(),
        ];
    }
}
