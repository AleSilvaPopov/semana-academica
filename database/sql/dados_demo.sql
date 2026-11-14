-- =====================================================================
-- Dados de DEMONSTRAÇÃO: 11ª edição (2027) com inscrições abertas.
--
-- Por quê: os dados de teste do script.sql vão até a 10ª edição (19 a 23/10/2026),
-- que já aconteceu. Como a regra de negócio só aceita inscrição ANTES da data da
-- atividade, sem uma edição futura não daria para demonstrar inscrição, lotação e
-- cancelamento. Este script só INSERE/ATUALIZA DADOS: nenhuma tabela, coluna,
-- trigger ou view é alterada.
--
-- Rode UMA vez, depois do script.sql (ou script_deploy.sql) e do senhas_teste.sql.
-- Em hospedagem compartilhada, apague a linha "USE" se o banco tiver outro nome.
-- =====================================================================

USE semana_academica;

-- A 10ª edição terminou: encerra as coordenações dela (estavam com término NULL).
-- Assim cada tarefa nova fica associada a uma única coordenação (a da 11ª edição).
UPDATE coordenadoria SET data_termino_coordenacao = '2026-10-31'
 WHERE id_comissao = 10 AND data_termino_coordenacao IS NULL;

-- Edição 11 (2027)
INSERT INTO comissao_organizadora (id_comissao, nome_evento, edicao, data_inicio, data_termino, local_sede, observacoes) VALUES
  (11, 'Semana Acadêmica de Computação da UFGD', 11, '2027-10-18', '2027-10-22', 'UFGD - Unidade II, Dourados/MS',
   'Edição de demonstração do sistema: inscrições abertas.');

-- Presidente e vice (mesmas pessoas da 10ª edição, para usar as credenciais de teste)
INSERT INTO presidencia (id_comissao, rga_presidente, rga_vice) VALUES (11, '2023104791', '2025109922');

-- Coordenadorias (coordenação começa em 01/11/2026, depois do ingresso de cada coordenador)
INSERT INTO coordenadoria (id_coordenadoria, nome, descricao_atuacao, id_comissao, rga_coordenador, data_inicio_coordenacao, data_termino_coordenacao) VALUES
  (111, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 11, '2026113855', '2026-11-01', NULL),
  (112, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 11, '2025106085', '2026-11-01', NULL),
  (113, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 11, '2025107381', '2026-11-01', NULL),
  (114, 'Programação Científica', 'Definição das atividades, convite a ministrantes e montagem da grade.', 11, '2025111220', '2026-11-01', NULL),
  (115, 'Financeiro', 'Orçamento, captação e prestação de contas dos patrocínios e das inscrições.', 11, '2026115158', '2026-11-01', NULL);

-- Atividades (vagas <= capacidade do local; datas dentro de 18 a 22/10/2027)
INSERT INTO atividade (id_atividade, nome, descricao, tipo, duracao_minutos, data_realizacao, vagas, valor_inscricao, status, id_local, id_coordenadoria) VALUES
  (13, 'Abertura: Computação e Sustentabilidade', 'Palestra de abertura sobre o impacto ambiental da computação e caminhos para uma TI mais sustentável.', 'palestra', 90, '2027-10-18 19:00:00', 380, 0.00, 'confirmada', 10, 114),
  (14, 'Git e GitHub na Prática', 'Minicurso de versionamento: commits, branches, pull requests e revisão de código. Turma reduzida.', 'minicurso', 240, '2027-10-19 13:30:00', 3, 15.00, 'confirmada', 2, 114),
  (15, 'Introdução ao Laravel', 'Minicurso de desenvolvimento web com PHP e Laravel: rotas, Eloquent, Blade e autenticação.', 'minicurso', 240, '2027-10-19 13:30:00', 40, 20.00, 'confirmada', 3, 114),
  (16, 'Segurança Ofensiva para Iniciantes', 'Workshop prático de reconhecimento, varredura e exploração em laboratório controlado.', 'workshop', 180, '2027-10-20 08:00:00', 30, 10.00, 'planejada', 4, 114),
  (17, 'Visualização de Dados com Python', 'Palestra sobre como contar histórias com dados usando matplotlib e plotly.', 'palestra', 60, '2027-10-21 10:00:00', 60, 0.00, 'confirmada', 5, 114),
  (18, 'Robótica Educacional com Arduino', 'Workshop cancelado por indisponibilidade dos kits (demonstra a regra de status).', 'workshop', 180, '2027-10-21 14:00:00', 25, 15.00, 'cancelada', 8, 114),
  (19, 'Cabeamento Estruturado e Redes Wi-Fi', 'Workshop de crimpagem, certificação de cabos e configuração de pontos de acesso.', 'workshop', 180, '2027-10-22 08:00:00', 30, 10.00, 'planejada', 4, 111);

INSERT INTO ministra (cpf_ministrante, id_atividade) VALUES
  ('91699854300', 13), ('46247510780', 13),
  ('93534874048', 14),
  ('11838425110', 15),
  ('64005242740', 16),
  ('84124118201', 17),
  ('68011280511', 18),
  ('15869232260', 19);

-- Inscrições (anteriores às atividades). A atividade 14 fica com 2 de 3 vagas ocupadas.
INSERT INTO inscricao (cpf, id_atividade, data_inscricao, presenca) VALUES
  ('65414586853', 14, '2026-11-10 10:00:00', FALSE),
  ('55698169330', 14, '2026-11-10 10:30:00', FALSE),
  ('65414586853', 13, '2026-11-10 10:05:00', FALSE),
  ('55698169330', 13, '2026-11-10 10:35:00', FALSE),
  ('06088356170', 13, '2026-11-11 09:00:00', FALSE),
  ('48236629970', 13, '2026-11-11 14:20:00', FALSE),
  ('37917693678', 15, '2026-11-12 08:15:00', FALSE),
  ('63201632830', 15, '2026-11-12 19:40:00', FALSE),
  ('79868727723', 17, '2026-11-13 11:00:00', FALSE);

-- Tarefas (criadas por coordenadores da 11ª edição; a 16 já está atrasada)
INSERT INTO tarefa (id_tarefa, descricao, data_criacao, prazo_final, status, rga_criador) VALUES
  (15, 'Reservar o auditório da Reitoria para a abertura de 2027.', '2026-11-05', '2026-12-15', 'pendente', '2026113855'),
  (16, 'Enviar os convites aos ministrantes da 11ª edição.', '2026-11-03', '2026-11-10', 'em_andamento', '2025111220'),
  (17, 'Atualizar o site com a programação de 2027.', '2026-11-06', '2026-12-01', 'pendente', '2025106085');

INSERT INTO tarefa_responsavel (id_tarefa, rga) VALUES
  (15, '2026116444'), (15, '2026121680'),
  (16, '2025109922'),
  (17, '2026117743'), (17, '2026116444');

-- Patrocínios da 11ª edição
INSERT INTO patrocinio (nome_patrocinador, id_comissao, tipo_patrocinio, descricao_patrocinio, valor, requisitos) VALUES
  ('Pantanal Software Ltda', 11, 'financeiro', 'Cota diamante', 6000.00, 'Palestra na abertura e estande no hall.'),
  ('Guaicurus Telecom', 11, 'servico', 'Link de internet dedicado de 1 Gbps', NULL, 'Banner no auditório.'),
  ('DataSul Cloud', 11, 'servico', 'Créditos de nuvem para 2 minicursos', NULL, 'Uso da plataforma nos minicursos.');
