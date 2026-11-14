<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Tratamento CENTRALIZADO dos erros do MySQL (registrado em bootstrap/app.php).
 *
 * Em vez de uma página de erro 500, o usuário volta para a tela anterior com uma
 * mensagem amigável:
 *  - 1451 / SQLSTATE 23000: exclusão bloqueada por FK -> "Não é possível excluir: existem X vinculados"
 *  - 45000: SIGNAL dos triggers trg_tarefa_criador_bi/bu -> mensagem do próprio trigger
 *  - 3819: CHECK constraint violada (segunda barreira, a validação já cobre)
 *  - 1062: UNIQUE/PK duplicada
 *  - 1452: FK inexistente ao inserir/alterar
 */
class ErrosBanco
{
    /** Nome amigável das tabelas filhas: [singular, plural, gênero]. */
    private const TABELAS = [
        'atividade' => ['atividade', 'atividades', 'f'],
        'coordenadoria' => ['coordenadoria', 'coordenadorias', 'f'],
        'integrante' => ['integrante', 'integrantes', 'm'],
        'inscricao' => ['inscrição', 'inscrições', 'f'],
        'tarefa' => ['tarefa criada', 'tarefas criadas', 'f'],
        'tarefa_responsavel' => ['tarefa com esta pessoa como responsável', 'tarefas com esta pessoa como responsável', 'f'],
        'presidencia' => ['presidência/vice-presidência de edição', 'presidências/vice-presidências de edição', 'f'],
        'patrocinio' => ['patrocínio', 'patrocínios', 'm'],
        'ministra' => ['atividade ministrada', 'atividades ministradas', 'f'],
        'ministrante' => ['cadastro de ministrante', 'cadastros de ministrante', 'm'],
    ];

    /** Mensagens das CHECK constraints do script.sql. */
    private const CHECKS = [
        'ck_comissao_datas' => 'A data de término da edição deve ser igual ou posterior à data de início.',
        'ck_coordenacao_datas' => 'O término da coordenação deve ser igual ou posterior ao início.',
        'ck_presidente_diferente_vice' => 'Presidente e vice-presidente devem ser pessoas diferentes.',
        'ck_tarefa_prazo' => 'O prazo final deve ser igual ou posterior à data de criação.',
        'ck_patrocinio_valor' => 'O valor do patrocínio não pode ser negativo.',
        'ck_local_capacidade' => 'A capacidade do local deve ser maior que zero.',
        'ck_atividade_duracao' => 'A duração da atividade deve ser maior que zero.',
        'ck_atividade_vagas' => 'O número de vagas deve ser maior que zero.',
        'ck_atividade_valor' => 'O valor da inscrição não pode ser negativo.',
    ];

    public static function renderizar(QueryException $e, Request $request): ?RedirectResponse
    {
        // Em GET não há formulário para onde voltar: deixa a página de erro padrão.
        if ($request->isMethod('GET') || $request->expectsJson()) {
            return null;
        }

        $mensagem = self::mensagemAmigavel($e, $request);

        if ($mensagem === null) {
            return null;
        }

        Log::info('Erro de banco convertido em mensagem amigável', ['sql' => $e->getMessage()]);

        return redirect()->back()->withInput($request->except(['senha', 'senha_confirmation', 'password', '_token', '_method']))
            ->with('erro', $mensagem);
    }

    public static function mensagemAmigavel(QueryException $e, ?Request $request = null): ?string
    {
        $sqlState = (string) ($e->errorInfo[0] ?? $e->getCode());
        $codigo = (int) ($e->errorInfo[1] ?? 0);
        $texto = (string) ($e->errorInfo[2] ?? $e->getMessage());

        return match (true) {
            $sqlState === '45000' => $texto,
            $codigo === 1451 => self::mensagemExclusao($texto, $request),
            $codigo === 3819 => self::mensagemCheck($texto),
            $codigo === 1062 => 'Já existe um registro com esses dados (valor duplicado em um campo que deve ser único).',
            $codigo === 1452 => 'Um dos registros relacionados escolhidos não existe mais. Atualize a página e tente de novo.',
            default => null,
        };
    }

    /**
     * Monta "Não é possível excluir: existem 3 atividades vinculadas."
     * A tabela filha e a coluna vêm da própria mensagem do MySQL; o valor da chave vem do
     * model recebido na rota (route model binding), o que permite contar os vínculos.
     */
    private static function mensagemExclusao(string $texto, ?Request $request): string
    {
        $padrao = '/fails \(`[^`]+`\.`(?<filha>[^`]+)`, CONSTRAINT `[^`]+` FOREIGN KEY \(`(?<coluna>[^`]+)`\) REFERENCES `(?<pai>[^`]+)` \(`(?<referencia>[^`]+)`\)\)/';

        if (! preg_match($padrao, $texto, $m)) {
            return 'Não é possível excluir: existem registros vinculados a este item.';
        }

        [$singular, $plural, $genero] = self::TABELAS[$m['filha']] ?? ['registro vinculado', 'registros vinculados', 'm'];
        $quantidade = self::contarVinculos($m['filha'], $m['coluna'], $m['pai'], $m['referencia'], $request);
        $sufixo = $genero === 'f' ? 'a' : 'o';

        if ($quantidade === null) {
            return "Não é possível excluir: existem {$plural} vinculad{$sufixo}s a este registro.";
        }

        if (str_contains($singular, ' ')) {
            // Frases compostas ("tarefa criada") já trazem a concordância.
            return $quantidade === 1
                ? "Não é possível excluir: existe 1 {$singular} vinculada a este registro."
                : "Não é possível excluir: existem {$quantidade} {$plural} vinculadas a este registro.";
        }

        return $quantidade === 1
            ? "Não é possível excluir: existe 1 {$singular} vinculad{$sufixo}."
            : "Não é possível excluir: existem {$quantidade} {$plural} vinculad{$sufixo}s.";
    }

    private static function contarVinculos(string $filha, string $coluna, string $pai, string $referencia, ?Request $request): ?int
    {
        if (! $request?->route()) {
            return null;
        }

        try {
            foreach ($request->route()->parameters() as $parametro) {
                if ($parametro instanceof Model && $parametro->getTable() === $pai) {
                    return DB::table($filha)->where($coluna, $parametro->getAttribute($referencia))->count();
                }
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    private static function mensagemCheck(string $texto): string
    {
        foreach (self::CHECKS as $nome => $mensagem) {
            if (str_contains($texto, $nome)) {
                return $mensagem;
            }
        }

        return 'Os dados informados violam uma regra do banco de dados.';
    }
}
