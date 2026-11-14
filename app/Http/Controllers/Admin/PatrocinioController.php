<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoPatrocinio;
use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatrocinioRequest;
use App\Models\Comissao;
use App\Models\Patrocinador;
use App\Models\Patrocinio;
use App\Services\EdicaoAtual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Patrocínios da edição selecionada: N:N COM ATRIBUTOS entre patrocinador e comissão.
 *
 * A PK é composta (nome_patrocinador, id_comissao), por isso as rotas levam as DUAS chaves
 * (/admin/patrocinios/{comissao}/{patrocinador}) e update/delete usam Query Builder
 * filtrando as duas colunas (o Eloquent não trabalha com chave composta).
 */
class PatrocinioController extends Controller
{
    use Listagem;

    public function index(Request $request, EdicaoAtual $edicao): View
    {
        $idComissao = $edicao->obrigatoria()->id_comissao;

        $query = Patrocinio::query()->where('patrocinio.id_comissao', $idComissao)->with('patrocinador');
        $this->buscar($query, $request->query('q'), ['nome_patrocinador', 'descricao_patrocinio', 'requisitos']);
        $query->when($request->query('tipo'), fn ($q, $v) => $q->where('tipo_patrocinio', $v));

        // Totais calculados sobre o filtro atual (antes da paginação).
        $total = (clone $query)->sum('valor');
        $porTipo = Patrocinio::query()->where('id_comissao', $idComissao)
            ->selectRaw('tipo_patrocinio, COUNT(*) as quantidade, COALESCE(SUM(valor), 0) as soma')
            ->groupBy('tipo_patrocinio')->get()->keyBy(fn ($l) => $l->tipo_patrocinio->value);

        $this->ordenar($query, $request, [
            'patrocinador' => 'nome_patrocinador',
            'tipo' => 'tipo_patrocinio',
            'valor' => 'valor',
        ], 'valor', 'desc');

        return view('admin.patrocinios.index', [
            'patrocinios' => $query->paginate($this->porPagina)->withQueryString(),
            'tipos' => TipoPatrocinio::opcoes(),
            'total' => $total,
            'porTipo' => $porTipo,
        ]);
    }

    public function show(Comissao $comissao, Patrocinador $patrocinador): View
    {
        $patrocinio = $this->buscarPatrocinio($comissao, $patrocinador);

        return view('admin.patrocinios.show', compact('patrocinio', 'comissao', 'patrocinador'));
    }

    public function create(EdicaoAtual $edicao): View
    {
        $comissao = $edicao->obrigatoria();
        $this->authorize('gerir-edicao', $comissao->id_comissao);

        return view('admin.patrocinios.create', [
            'patrocinio' => new Patrocinio(),
            'comissao' => $comissao,
            'tipos' => TipoPatrocinio::opcoes(),
            // Só patrocinadores que ainda não apoiam esta edição (a PK composta não deixa repetir).
            'patrocinadores' => Patrocinador::whereDoesntHave('patrocinios', fn ($q) => $q->where('id_comissao', $comissao->id_comissao))
                ->orderBy('nome')->pluck('nome', 'nome'),
        ]);
    }

    public function store(PatrocinioRequest $request): RedirectResponse
    {
        $comissao = Comissao::findOrFail($request->idComissao());
        $dados = $request->validated();

        // attach() insere a linha na tabela N:N já com os atributos do relacionamento (pivot).
        $comissao->patrocinadores()->attach($dados['nome_patrocinador'], collect($dados)->except('nome_patrocinador')->all());

        return redirect()->route('admin.patrocinios.index')->with('sucesso', 'Patrocínio de '.$dados['nome_patrocinador'].' cadastrado.');
    }

    public function edit(Comissao $comissao, Patrocinador $patrocinador): View
    {
        $this->authorize('gerir-edicao', $comissao->id_comissao);

        return view('admin.patrocinios.edit', [
            'patrocinio' => $this->buscarPatrocinio($comissao, $patrocinador),
            'comissao' => $comissao,
            'patrocinador' => $patrocinador,
            'tipos' => TipoPatrocinio::opcoes(),
        ]);
    }

    public function update(PatrocinioRequest $request, Comissao $comissao, Patrocinador $patrocinador): RedirectResponse
    {
        $this->buscarPatrocinio($comissao, $patrocinador);

        // UPDATE ... WHERE id_comissao = ? AND nome_patrocinador = ?  (PK composta)
        DB::table('patrocinio')
            ->where('id_comissao', $comissao->id_comissao)
            ->where('nome_patrocinador', $patrocinador->nome)
            ->update($request->validated());

        return redirect()->route('admin.patrocinios.index')->with('sucesso', 'Patrocínio atualizado.');
    }

    public function destroy(Comissao $comissao, Patrocinador $patrocinador): RedirectResponse
    {
        $this->authorize('gerir-edicao', $comissao->id_comissao);

        DB::table('patrocinio')
            ->where('id_comissao', $comissao->id_comissao)
            ->where('nome_patrocinador', $patrocinador->nome)
            ->delete();

        return redirect()->route('admin.patrocinios.index')->with('sucesso', 'Patrocínio removido da edição.');
    }

    private function buscarPatrocinio(Comissao $comissao, Patrocinador $patrocinador): Patrocinio
    {
        return Patrocinio::daChave($comissao->id_comissao, $patrocinador->nome)->firstOrFail();
    }
}
