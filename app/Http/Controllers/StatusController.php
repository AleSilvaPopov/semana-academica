<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

/**
 * Página /status: prova a conexão com o banco (Parte 1).
 * Liberada só com APP_DEBUG=true ou para presidente/vice logado no painel.
 */
class StatusController extends Controller
{
    /** As 20 tabelas do script.sql, na ordem de criação. */
    public const TABELAS = [
        'comissao_organizadora', 'curso', 'integrante', 'integrante_telefone', 'integrante_email',
        'coordenadoria', 'presidencia', 'tarefa', 'tarefa_responsavel', 'patrocinador',
        'patrocinio', 'participante', 'participante_telefone', 'ministrante', 'ministrante_certificado',
        'local_evento', 'local_equipamento', 'atividade', 'ministra', 'inscricao',
    ];

    public function __invoke(): View
    {
        $usuario = auth('integrante')->user();
        abort_unless(config('app.debug') || ($usuario && $usuario->can('cadastros-globais')), 403,
            'A página de status só fica disponível com APP_DEBUG=true ou para presidente/vice logado.');

        $erro = null;
        $info = [];
        $contagens = [];
        $objetos = ['triggers' => [], 'views' => [], 'indices' => []];

        try {
            $info = (array) DB::selectOne('SELECT DATABASE() AS banco, VERSION() AS versao, @@collation_database AS collation, @@character_set_database AS charset, NOW() AS agora');

            foreach (self::TABELAS as $tabela) {
                $contagens[$tabela] = DB::table($tabela)->count();
            }

            $banco = $info['banco'];
            $objetos['triggers'] = DB::select('SELECT TRIGGER_NAME AS nome, EVENT_MANIPULATION AS evento, ACTION_TIMING AS momento, EVENT_OBJECT_TABLE AS tabela FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = ? ORDER BY TRIGGER_NAME', [$banco]);
            $objetos['views'] = DB::select('SELECT TABLE_NAME AS nome FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?', [$banco]);
            $objetos['indices'] = DB::select("SELECT DISTINCT INDEX_NAME AS nome, TABLE_NAME AS tabela FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND INDEX_NAME LIKE 'idx\\_%' ORDER BY INDEX_NAME", [$banco]);
        } catch (Throwable $e) {
            $erro = $e->getMessage();
        }

        return view('status', [
            'erro' => $erro,
            'info' => $info,
            'contagens' => $contagens,
            'total' => array_sum($contagens),
            'objetos' => $objetos,
            'conexao' => config('database.connections.'.config('database.default')),
        ]);
    }
}
