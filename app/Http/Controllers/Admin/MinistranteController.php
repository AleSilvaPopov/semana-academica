<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Listagem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MinistranteRequest;
use App\Models\Ministrante;
use App\Models\Participante;
use App\Services\Multivalorado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Ministrantes (especialização de participante), com foto de divulgação (upload no disco
 * "public", caminho salvo em imagem_divulgacao) e certificados (atributo multivalorado).
 */
class MinistranteController extends Controller
{
    use Listagem;

    public function index(Request $request): View
    {
        $query = Ministrante::query()
            ->join('participante', 'participante.cpf', '=', 'ministrante.cpf')
            ->select('ministrante.*')
            ->with('participante')
            ->withCount(['certificados', 'atividades']);

        $this->buscar($query, $request->query('q'), ['participante.nome_completo', 'participante.instituicao_origem', 'participante.cpf']);
        $query->when($request->query('foto') === 'com', fn ($q) => $q->whereNotNull('ministrante.imagem_divulgacao'))
            ->when($request->query('foto') === 'sem', fn ($q) => $q->whereNull('ministrante.imagem_divulgacao'));
        $this->ordenar($query, $request, [
            'nome' => 'participante.nome_completo',
            'instituicao' => 'participante.instituicao_origem',
            'atividades' => 'atividades_count',
        ], 'nome');

        return view('admin.ministrantes.index', ['ministrantes' => $query->paginate($this->porPagina)->withQueryString()]);
    }

    public function show(Ministrante $ministrante): View
    {
        $ministrante->load([
            'participante.telefones',
            'certificados',
            'atividades' => fn ($q) => $q->with('coordenadoria.comissao', 'local')->orderByDesc('data_realizacao'),
        ]);

        return view('admin.ministrantes.show', compact('ministrante'));
    }

    public function create(Request $request): View
    {
        $this->authorize('cadastros-globais');

        return view('admin.ministrantes.create', [
            'ministrante' => new Ministrante(['cpf' => $request->query('cpf')]),
            'participantes' => $this->participantesDisponiveis(),
        ]);
    }

    public function store(MinistranteRequest $request): RedirectResponse
    {
        $caminho = $this->salvarImagem($request->file('imagem'));

        $ministrante = DB::transaction(function () use ($request, $caminho) {
            $ministrante = Ministrante::create(['cpf' => $request->validated('cpf'), 'imagem_divulgacao' => $caminho]);
            Multivalorado::sincronizar($ministrante->certificados(), 'certificado', $request->validated('certificados', []));

            return $ministrante;
        });

        return redirect()->route('admin.ministrantes.show', $ministrante)->with('sucesso', 'Ministrante cadastrado com sucesso.');
    }

    public function edit(Ministrante $ministrante): View
    {
        $this->authorize('cadastros-globais');
        $ministrante->load('participante', 'certificados');

        return view('admin.ministrantes.edit', compact('ministrante'));
    }

    public function update(MinistranteRequest $request, Ministrante $ministrante): RedirectResponse
    {
        $antigo = $ministrante->imagem_divulgacao;
        $novo = $antigo;

        if ($request->hasFile('imagem')) {
            $novo = $this->salvarImagem($request->file('imagem'));
        } elseif ($request->boolean('remover_imagem')) {
            $novo = null;
        }

        DB::transaction(function () use ($request, $ministrante, $novo) {
            $ministrante->update(['imagem_divulgacao' => $novo]);
            Multivalorado::sincronizar($ministrante->certificados(), 'certificado', $request->validated('certificados', []));
        });

        if ($novo !== $antigo) {
            $this->apagarImagem($antigo);
        }

        return redirect()->route('admin.ministrantes.show', $ministrante)->with('sucesso', 'Ministrante atualizado com sucesso.');
    }

    public function destroy(Ministrante $ministrante): RedirectResponse
    {
        $this->authorize('cadastros-globais');
        $imagem = $ministrante->imagem_divulgacao;

        // Certificados saem por CASCADE; atividades ministradas (tabela ministra) bloqueiam com erro 1451.
        // O participante continua cadastrado: só a especialização é removida.
        $ministrante->delete();
        $this->apagarImagem($imagem);

        return redirect()->route('admin.ministrantes.index')->with('sucesso', 'Ministrante removido (o participante continua cadastrado).');
    }

    /** Participantes que ainda não são ministrantes, para o <select> do cadastro. */
    private function participantesDisponiveis()
    {
        return Participante::query()
            ->whereDoesntHave('ministrante')
            ->orderBy('nome_completo')
            ->get(['cpf', 'nome_completo'])
            ->mapWithKeys(fn ($p) => [$p->cpf => $p->nome_completo.' ('.formata_cpf($p->cpf).')']);
    }

    private function salvarImagem(?UploadedFile $arquivo): ?string
    {
        if (! $arquivo) {
            return null;
        }

        $nome = Str::uuid().'.'.$arquivo->extension();

        return $arquivo->storeAs('ministrantes', $nome, 'public'); // ex.: ministrantes/uuid.jpg (cabe em VARCHAR(255))
    }

    /** Só apaga arquivos enviados pelo sistema (os caminhos img/... dos dados de teste não são tocados). */
    private function apagarImagem(?string $caminho): void
    {
        if ($caminho && str_starts_with($caminho, 'ministrantes/')) {
            Storage::disk('public')->delete($caminho);
        }
    }
}
