<?php

namespace App\Support;

/**
 * Itens da sidebar do painel, agrupados.
 * "ativo" é o padrão de nome de rota que deixa o item destacado.
 */
class MenuAdmin
{
    /**
     * @return array<int, array{grupo: string, itens: array<int, array<string, mixed>>}>
     */
    public static function grupos(): array
    {
        return [
            [
                'grupo' => 'Visão geral',
                'itens' => [
                    ['rota' => 'admin.dashboard', 'rotulo' => 'Dashboard', 'icone' => 'chart', 'ativo' => 'admin.dashboard'],
                ],
            ],
            [
                'grupo' => 'Edição selecionada',
                'itens' => [
                    ['rota' => 'admin.comissoes.index', 'rotulo' => 'Edições', 'icone' => 'flag', 'ativo' => 'admin.comissoes.*'],
                    ['rota' => 'admin.coordenadorias.index', 'rotulo' => 'Coordenadorias', 'icone' => 'squares', 'ativo' => 'admin.coordenadorias.*'],
                    ['rota' => 'admin.atividades.index', 'rotulo' => 'Atividades', 'icone' => 'presentation', 'ativo' => 'admin.atividades.*'],
                    ['rota' => 'admin.inscricoes.index', 'rotulo' => 'Inscrições e presença', 'icone' => 'ticket', 'ativo' => 'admin.inscricoes.*'],
                    ['rota' => 'admin.certificados.index', 'rotulo' => 'Certificados', 'icone' => 'document', 'ativo' => 'admin.certificados.*'],
                    ['rota' => 'admin.tarefas.index', 'rotulo' => 'Tarefas', 'icone' => 'clipboard', 'ativo' => 'admin.tarefas.*'],
                    ['rota' => 'admin.minhas-tarefas', 'rotulo' => 'Minhas tarefas', 'icone' => 'check', 'ativo' => 'admin.minhas-tarefas'],
                    ['rota' => 'admin.patrocinios.index', 'rotulo' => 'Patrocínios', 'icone' => 'money', 'ativo' => 'admin.patrocinios.*'],
                ],
            ],
            [
                'grupo' => 'Cadastros gerais',
                'itens' => [
                    ['rota' => 'admin.integrantes.index', 'rotulo' => 'Integrantes', 'icone' => 'users', 'ativo' => 'admin.integrantes.*'],
                    ['rota' => 'admin.cursos.index', 'rotulo' => 'Cursos', 'icone' => 'academic', 'ativo' => 'admin.cursos.*'],
                    ['rota' => 'admin.participantes.index', 'rotulo' => 'Participantes', 'icone' => 'user', 'ativo' => 'admin.participantes.*'],
                    ['rota' => 'admin.ministrantes.index', 'rotulo' => 'Ministrantes', 'icone' => 'microphone', 'ativo' => 'admin.ministrantes.*'],
                    ['rota' => 'admin.locais.index', 'rotulo' => 'Locais', 'icone' => 'map', 'ativo' => 'admin.locais.*'],
                    ['rota' => 'admin.patrocinadores.index', 'rotulo' => 'Patrocinadores', 'icone' => 'briefcase', 'ativo' => 'admin.patrocinadores.*'],
                ],
            ],
            [
                'grupo' => 'Sistema',
                'itens' => [
                    ['rota' => 'status', 'rotulo' => 'Status do banco', 'icone' => 'server', 'ativo' => 'status'],
                ],
            ],
        ];
    }
}
