-- =====================================================================
-- Semana Acadêmica - script de DEPLOY (MySQL 8.0+)
-- Gerado a partir de database/sql/script.sql (mesmas tabelas, chaves, CHECKs,
-- triggers, view, índices e dados de teste).
--
-- Diferenças em relação ao script.sql:
--   * sem DROP DATABASE / CREATE DATABASE / USE: em hospedagem o banco já existe
--     e tem nome fixo. Selecione o banco (phpMyAdmin) ou rode:
--       mysql -h HOST -P PORTA -u USUARIO -p NOME_DO_BANCO < script_deploy.sql
--   * charset/collation explícitos em cada tabela (utf8mb4_0900_ai_ci).
--   * sem os SHOW INDEX de conferência.
--
-- Triggers: com binary log ativo, criar trigger pode exigir SUPER ou
-- log_bin_trust_function_creators=1 (erro 1419). Se o host bloquear, rode este script
-- sem o bloco "TRIGGERS" e crie os dois triggers pela aba "Gatilhos/Triggers" do
-- phpMyAdmin (o código está no bloco abaixo). A aplicação também valida a regra
-- "só coordenador cria tarefa" no Form Request (App\Http\Requests\Admin\TarefaRequest).
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Semana Acadêmica - script completo
-- =====================================================================

-- =====================================================================
-- Projeto Físico de Banco de Dados - Semana Acadêmica / Congresso
-- Alunos: Gabriel Fabricio Meirelles Peu e Alexandre da Silva Popov
-- SGBD: MySQL 8.0
-- =====================================================================

-- Script de Criação: criação do banco, das tabelas, restrições, trigger e view

 
-- 1. COMISSÃO ORGANIZADORA (Regra 1 - entidade forte)
--   Uma linha por edição: mantém o histórico de comissões anteriores.

CREATE TABLE comissao_organizadora (
  id_comissao   INT           NOT NULL,
  nome_evento   VARCHAR(150)  NOT NULL,
  edicao        SMALLINT      NOT NULL,
  data_inicio   DATE          NOT NULL,
  data_termino  DATE          NOT NULL,
  local_sede    VARCHAR(150)  NOT NULL,
  observacoes   TEXT          NULL,
  CONSTRAINT pk_comissao PRIMARY KEY (id_comissao),
  CONSTRAINT uq_comissao_evento_edicao UNIQUE (nome_evento, edicao),
  CONSTRAINT ck_comissao_datas CHECK (data_termino >= data_inicio)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

 
-- 2. CURSO (tabela criada na normalização - 3FN)
--    Evita a dependência transitiva RGA -> curso -> faculdade.
 
CREATE TABLE curso (
  id_curso    INT           NOT NULL,
  nome_curso  VARCHAR(100)  NOT NULL,
  faculdade   VARCHAR(150)  NOT NULL,
  CONSTRAINT pk_curso PRIMARY KEY (id_curso),
  CONSTRAINT uq_curso_nome_faculdade UNIQUE (nome_curso, faculdade)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

 
-- 3. INTEGRANTE (Regra 1 + Regra 4 do relacionamento "participa" 1:N)
--    id_coordenadoria aceita NULL: participação parcial do integrante.
--    A FK para coordenadoria é criada depois (ALTER TABLE), pois
--    coordenadoria também referencia integrante (coordenador).
 
CREATE TABLE integrante (
  rga               VARCHAR(20)   NOT NULL,
  cpf               CHAR(11)      NOT NULL,
  nome_completo     VARCHAR(150)  NOT NULL,
  id_curso          INT           NOT NULL,
  senha_hash        CHAR(64)      NOT NULL,
  funcao            VARCHAR(60)   NOT NULL,
  data_ingresso     DATE          NOT NULL,
  observacoes       TEXT          NULL,
  id_coordenadoria  INT           NULL,
  CONSTRAINT pk_integrante PRIMARY KEY (rga),
  CONSTRAINT uq_integrante_cpf UNIQUE (cpf),
  CONSTRAINT fk_integrante_curso FOREIGN KEY (id_curso)
    REFERENCES curso (id_curso)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 4. Telefones do integrante (Regra 6 - atributo multivalorado)
CREATE TABLE integrante_telefone (
  rga       VARCHAR(20)  NOT NULL,
  telefone  VARCHAR(15)  NOT NULL,
  CONSTRAINT pk_integrante_telefone PRIMARY KEY (rga, telefone),
  CONSTRAINT fk_int_tel_integrante FOREIGN KEY (rga)
    REFERENCES integrante (rga) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 5. E-mails do integrante (Regra 6 - atributo multivalorado)
CREATE TABLE integrante_email (
  rga    VARCHAR(20)   NOT NULL,
  email  VARCHAR(120)  NOT NULL,
  CONSTRAINT pk_integrante_email PRIMARY KEY (rga, email),
  CONSTRAINT fk_int_email_integrante FOREIGN KEY (rga)
    REFERENCES integrante (rga) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

 
-- 6. COORDENADORIA
--    Regra 4 ("Pertence_a" N:1 com comissão): FK id_comissao.
--    Regra 3 ("Coordena" 1:1): participação total da coordenadoria,
--    então a PK do integrante entra aqui como FK (rga_coordenador),
--    junto com as datas da coordenação (especialização Coordenador).
--    O atributo derivado "Quantidade Integrantes" virou a view
--    vw_quantidade_integrantes.
 
CREATE TABLE coordenadoria (
  id_coordenadoria          INT           NOT NULL,
  nome                      VARCHAR(80)   NOT NULL,
  descricao_atuacao         TEXT          NOT NULL,
  id_comissao               INT           NOT NULL,
  rga_coordenador           VARCHAR(20)   NOT NULL,
  data_inicio_coordenacao   DATE          NOT NULL,
  data_termino_coordenacao  DATE          NULL,

  CONSTRAINT pk_coordenadoria PRIMARY KEY (id_coordenadoria),

  CONSTRAINT uq_coordenadoria_comissao_nome UNIQUE (id_comissao, nome),

  CONSTRAINT fk_coordenadoria_comissao FOREIGN KEY (id_comissao)
    REFERENCES comissao_organizadora (id_comissao),
  CONSTRAINT fk_coordenadoria_coordenador FOREIGN KEY (rga_coordenador)
    REFERENCES integrante (rga),
  CONSTRAINT ck_coordenacao_datas CHECK (data_termino_coordenacao IS NULL
    OR data_termino_coordenacao >= data_inicio_coordenacao)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Fecha o ciclo integrante para coordenadoria
ALTER TABLE integrante
  ADD CONSTRAINT fk_integrante_coordenadoria FOREIGN KEY (id_coordenadoria)
    REFERENCES coordenadoria (id_coordenadoria);


-- 7. PRESIDÊNCIA (especialização Presidente/Vice de integrante)
--    Cada comissão tem exatamente um presidente e um vice.
--    A mesma pessoa pode presidir outras edições.

CREATE TABLE presidencia (
  id_comissao     INT          NOT NULL,
  rga_presidente  VARCHAR(20)  NOT NULL,
  rga_vice        VARCHAR(20)  NOT NULL,

  CONSTRAINT pk_presidencia PRIMARY KEY (id_comissao),

  CONSTRAINT fk_presidencia_comissao FOREIGN KEY (id_comissao)
    REFERENCES comissao_organizadora (id_comissao),
  CONSTRAINT fk_presidencia_presidente FOREIGN KEY (rga_presidente)
    REFERENCES integrante (rga),
  CONSTRAINT fk_presidencia_vice FOREIGN KEY (rga_vice)
    REFERENCES integrante (rga),
  CONSTRAINT ck_presidente_diferente_vice CHECK (rga_presidente <> rga_vice)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 8. TAREFA (Regra 1 + Regra 4 do relacionamento "Cria" 1:N)
--    rga_criador deve ser coordenador (que é garantido pelo trigger).

CREATE TABLE tarefa (
  id_tarefa     INT          NOT NULL,
  descricao     TEXT         NOT NULL,
  data_criacao  DATE         NOT NULL,
  prazo_final   DATE         NOT NULL,
  status        ENUM('pendente','em_andamento','concluida','cancelada')
   NOT NULL DEFAULT 'pendente',

  rga_criador   VARCHAR(20)  NOT NULL,
  CONSTRAINT pk_tarefa PRIMARY KEY (id_tarefa),
  CONSTRAINT fk_tarefa_criador FOREIGN KEY (rga_criador)
    REFERENCES integrante (rga),
  CONSTRAINT ck_tarefa_prazo CHECK (prazo_final >= data_criacao)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 9. Responsáveis pela tarefa (Regra 5 - relacionamento "Recebe" N:N)
--    O nome do responsável NÃO é repetido aqui (3FN): obtém-se por JOIN.
CREATE TABLE tarefa_responsavel (
  id_tarefa  INT          NOT NULL,
  rga        VARCHAR(20)  NOT NULL,
  CONSTRAINT pk_tarefa_responsavel PRIMARY KEY (id_tarefa, rga),
  CONSTRAINT fk_tar_resp_tarefa FOREIGN KEY (id_tarefa)
    REFERENCES tarefa (id_tarefa) ON DELETE CASCADE,
  CONSTRAINT fk_tar_resp_integrante FOREIGN KEY (rga)
    REFERENCES integrante (rga)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 10. PATROCINADOR (Regra 1 - chave "Nome" conforme o DER)

CREATE TABLE patrocinador (
  nome         VARCHAR(120)  NOT NULL,
  observacoes  TEXT          NULL,
  CONSTRAINT pk_patrocinador PRIMARY KEY (nome)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 11. PATROCÍNIO (Regra 5 - "Patrocina" N:N, conforme o levantamento:
--     um patrocinador patrocina várias edições e uma edição tem vários
--     patrocinadores). O atributo composto "Patrocínio" foi decomposto
--     em tipo_patrocinio e descricao_patrocinio (Regra 1).
CREATE TABLE patrocinio (
  nome_patrocinador     VARCHAR(120)  NOT NULL,
  id_comissao           INT           NOT NULL,
  tipo_patrocinio       ENUM('financeiro','equipamento','servico','material')
                                      NOT NULL,
  descricao_patrocinio  VARCHAR(255)  NOT NULL,
  valor                 DECIMAL(10,2) NULL,
  requisitos            TEXT          NULL,
  CONSTRAINT pk_patrocinio PRIMARY KEY (nome_patrocinador, id_comissao),
  CONSTRAINT fk_patrocinio_patrocinador FOREIGN KEY (nome_patrocinador)
    REFERENCES patrocinador (nome),
  CONSTRAINT fk_patrocinio_comissao FOREIGN KEY (id_comissao)
    REFERENCES comissao_organizadora (id_comissao),
  CONSTRAINT ck_patrocinio_valor CHECK (valor IS NULL OR valor >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 12. PARTICIPANTE (Regra 1)

CREATE TABLE participante (
  cpf                 CHAR(11)      NOT NULL,
  nome_completo       VARCHAR(150)  NOT NULL,
  email               VARCHAR(120)  NOT NULL,
  senha_hash          CHAR(64)      NOT NULL,
  pais                VARCHAR(60)   NOT NULL DEFAULT 'Brasil',
  data_nascimento     DATE          NOT NULL,
  status_conta        ENUM('ativo','inativo','bloqueado')
    NOT NULL DEFAULT 'ativo',
  tipo_participante   ENUM('discente','docente','externo') NOT NULL,
  instituicao_origem  VARCHAR(150)  NOT NULL,
  CONSTRAINT pk_participante PRIMARY KEY (cpf),
  CONSTRAINT uq_participante_email UNIQUE (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 13. Telefones do participante (Regra 6 - atributo multivalorado)
CREATE TABLE participante_telefone (
  cpf       CHAR(11)     NOT NULL,
  telefone  VARCHAR(15)  NOT NULL,
  CONSTRAINT pk_participante_telefone PRIMARY KEY (cpf, telefone),
  CONSTRAINT fk_part_tel_participante FOREIGN KEY (cpf)
    REFERENCES participante (cpf) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 14. MINISTRANTE (especialização de participante: PK = FK)

CREATE TABLE ministrante (
  cpf                CHAR(11)      NOT NULL,
  imagem_divulgacao  VARCHAR(255)  NULL,
  CONSTRAINT pk_ministrante PRIMARY KEY (cpf),
  CONSTRAINT fk_ministrante_participante FOREIGN KEY (cpf)
    REFERENCES participante (cpf) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 15. Certificados do ministrante (Regra 6 - atributo multivalorado)
CREATE TABLE ministrante_certificado (
  cpf          CHAR(11)      NOT NULL,
  certificado  VARCHAR(200)  NOT NULL,
  CONSTRAINT pk_ministrante_certificado PRIMARY KEY (cpf, certificado),
  CONSTRAINT fk_min_cert_ministrante FOREIGN KEY (cpf)
    REFERENCES ministrante (cpf) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 16. LOCAL (Regra 1). "local" é palavra-chave do MySQL, por isso o
--     nome local_evento. O atributo composto "Localização" foi
--     decomposto em faculdade, bloco e numero.

CREATE TABLE local_evento (
  id_local     INT           NOT NULL,
  tipo         ENUM('auditorio','laboratorio','sala') NOT NULL,
  faculdade    VARCHAR(150)  NOT NULL,
  bloco        VARCHAR(20)   NOT NULL,
  numero       VARCHAR(10)   NOT NULL,
  capacidade   INT           NOT NULL,
  observacoes  TEXT          NULL,
  CONSTRAINT pk_local_evento PRIMARY KEY (id_local),
  CONSTRAINT uq_local_endereco UNIQUE (faculdade, bloco, numero),
  CONSTRAINT ck_local_capacidade CHECK (capacidade > 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 17. Equipamentos do local (Regra 6 - atributo multivalorado)
CREATE TABLE local_equipamento (
  id_local     INT          NOT NULL,
  equipamento  VARCHAR(60)  NOT NULL,
  CONSTRAINT pk_local_equipamento PRIMARY KEY (id_local, equipamento),
  CONSTRAINT fk_loc_equip_local FOREIGN KEY (id_local)
    REFERENCES local_evento (id_local) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- 18. ATIVIDADE (Regra 1 + Regra 4 de "Acontece em" e "Organiza")
--     Participação total da atividade nos dois: FKs NOT NULL.

CREATE TABLE atividade (
  id_atividade      INT           NOT NULL,
  nome              VARCHAR(150)  NOT NULL,
  descricao         TEXT          NOT NULL,
  tipo              ENUM('palestra','minicurso','workshop') NOT NULL,
  duracao_minutos   SMALLINT      NOT NULL,
  data_realizacao   DATETIME      NOT NULL,
  vagas             INT           NOT NULL,
  valor_inscricao   DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
  status            ENUM('planejada','confirmada','cancelada','realizada')
                                  NOT NULL DEFAULT 'planejada',
  id_local          INT           NOT NULL,
  id_coordenadoria  INT           NOT NULL,
  CONSTRAINT pk_atividade PRIMARY KEY (id_atividade),
  CONSTRAINT fk_atividade_local FOREIGN KEY (id_local)
    REFERENCES local_evento (id_local),
  CONSTRAINT fk_atividade_coordenadoria FOREIGN KEY (id_coordenadoria)
    REFERENCES coordenadoria (id_coordenadoria),
  CONSTRAINT ck_atividade_duracao CHECK (duracao_minutos > 0),
  CONSTRAINT ck_atividade_vagas CHECK (vagas > 0),
  CONSTRAINT ck_atividade_valor CHECK (valor_inscricao >= 0)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 19. MINISTRA (Regra 5 - N:N entre ministrante e atividade)
CREATE TABLE ministra (
  cpf_ministrante  CHAR(11)  NOT NULL,
  id_atividade     INT       NOT NULL,
  CONSTRAINT pk_ministra PRIMARY KEY (cpf_ministrante, id_atividade),
  CONSTRAINT fk_ministra_ministrante FOREIGN KEY (cpf_ministrante)
    REFERENCES ministrante (cpf),
  CONSTRAINT fk_ministra_atividade FOREIGN KEY (id_atividade)
    REFERENCES atividade (id_atividade) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 20. INSCRIÇÃO (Regra 5 - relacionamento "Participa" N:N)
--     presenca = 1 dá direito ao certificado de participação.
CREATE TABLE inscricao (
  cpf             CHAR(11)  NOT NULL,
  id_atividade    INT       NOT NULL,
  data_inscricao  DATETIME  NOT NULL,
  presenca        BOOLEAN   NOT NULL DEFAULT FALSE,
  CONSTRAINT pk_inscricao PRIMARY KEY (cpf, id_atividade),
  CONSTRAINT fk_inscricao_participante FOREIGN KEY (cpf)
    REFERENCES participante (cpf),
  CONSTRAINT fk_inscricao_atividade FOREIGN KEY (id_atividade)
    REFERENCES atividade (id_atividade) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;


-- TRIGGERS: só um coordenador pode criar tarefas

DELIMITER $$

CREATE TRIGGER trg_tarefa_criador_bi
BEFORE INSERT ON tarefa
FOR EACH ROW
BEGIN
  IF NOT EXISTS (SELECT 1 FROM coordenadoria c
                 WHERE c.rga_coordenador = NEW.rga_criador) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Somente coordenadores podem criar tarefas.';
  END IF;
END$$

CREATE TRIGGER trg_tarefa_criador_bu
BEFORE UPDATE ON tarefa
FOR EACH ROW
BEGIN
  IF NOT EXISTS (SELECT 1 FROM coordenadoria c
                 WHERE c.rga_coordenador = NEW.rga_criador) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Somente coordenadores podem criar tarefas.';
  END IF;
END$$

DELIMITER ;


-- VIEW: atributo derivado "Quantidade Integrantes" da coordenadoria

CREATE VIEW vw_quantidade_integrantes AS
SELECT c.id_coordenadoria,
       c.nome            AS coordenadoria,
       co.nome_evento,
       co.edicao,
       COUNT(i.rga)      AS quantidade_integrantes
FROM coordenadoria c
JOIN comissao_organizadora co ON co.id_comissao = c.id_comissao
LEFT JOIN integrante i        ON i.id_coordenadoria = c.id_coordenadoria
GROUP BY c.id_coordenadoria, c.nome, co.nome_evento, co.edicao;


-- Script de Inserção: inserção dos dados de teste (mínimo de 10 linhas por tabela)




-- comissao_organizadora (10 registros)
INSERT INTO comissao_organizadora (id_comissao, nome_evento, edicao, data_inicio, data_termino, local_sede, observacoes) VALUES
  (1, 'Semana Acadêmica de Computação da UFGD', 1, '2017-10-16', '2017-10-20', 'UFGD - Unidade II, Dourados/MS', NULL),
  (2, 'Semana Acadêmica de Computação da UFGD', 2, '2018-10-15', '2018-10-19', 'UFGD - Unidade II, Dourados/MS', NULL),
  (3, 'Semana Acadêmica de Computação da UFGD', 3, '2019-10-21', '2019-10-25', 'UFGD - Unidade II, Dourados/MS', NULL),
  (4, 'Semana Acadêmica de Computação da UFGD', 4, '2020-10-19', '2020-10-23', 'Online (YouTube e Google Meet)', 'Edição realizada de forma remota por causa da pandemia de COVID-19.'),
  (5, 'Semana Acadêmica de Computação da UFGD', 5, '2021-10-18', '2021-10-22', 'UFGD - Unidade II, Dourados/MS', 'Edição híbrida: palestras presenciais com transmissão ao vivo.'),
  (6, 'Semana Acadêmica de Computação da UFGD', 6, '2022-10-17', '2022-10-21', 'UFGD - Unidade II, Dourados/MS', NULL),
  (7, 'Semana Acadêmica de Computação da UFGD', 7, '2023-10-16', '2023-10-20', 'UFGD - Unidade II, Dourados/MS', NULL),
  (8, 'Semana Acadêmica de Computação da UFGD', 8, '2024-10-21', '2024-10-25', 'UFGD - Unidade II, Dourados/MS', NULL),
  (9, 'Semana Acadêmica de Computação da UFGD', 9, '2025-10-20', '2025-10-24', 'UFGD - Unidade II, Dourados/MS', NULL),
  (10, 'Semana Acadêmica de Computação da UFGD', 10, '2026-10-19', '2026-10-23', 'UFGD - Unidade II, Dourados/MS', 'Edição em planejamento.');

-- curso (10 registros)
INSERT INTO curso (id_curso, nome_curso, faculdade) VALUES
  (1, 'Ciência da Computação', 'Faculdade de Ciências Exatas e Tecnologia (FACET)'),
  (2, 'Sistemas de Informação', 'Faculdade de Ciências Exatas e Tecnologia (FACET)'),
  (3, 'Engenharia de Computação', 'Faculdade de Ciências Exatas e Tecnologia (FACET)'),
  (4, 'Matemática', 'Faculdade de Ciências Exatas e Tecnologia (FACET)'),
  (5, 'Física', 'Faculdade de Ciências Exatas e Tecnologia (FACET)'),
  (6, 'Engenharia de Produção', 'Faculdade de Engenharia (FAEN)'),
  (7, 'Engenharia de Energia', 'Faculdade de Engenharia (FAEN)'),
  (8, 'Administração', 'Faculdade de Administração, Ciências Contábeis e Economia (FACE)'),
  (9, 'Ciências Econômicas', 'Faculdade de Administração, Ciências Contábeis e Economia (FACE)'),
  (10, 'Engenharia Mecânica', 'Faculdade de Engenharia (FAEN)');

-- integrante (24 registros)
INSERT INTO integrante (rga, cpf, nome_completo, id_curso, senha_hash, funcao, data_ingresso, observacoes, id_coordenadoria) VALUES
  ('2023104791', '10433218100', 'Mariana Albuquerque Rocha', 1, 'e1de5aa0ec24abb440a418d9a5dd1100cd4752b15c9ec1a9167fddc66af172be', 'Presidente', '2023-03-10', NULL, NULL),
  ('2025106085', '60013389068', 'Lucas Henrique Vasconcelos', 2, '9924fe8820ac603dcafd3bc7d0bd6a44b96f0077ac34aeee2ac5199d7fa98935', 'Coordenador', '2025-03-12', NULL, NULL),
  ('2025107381', '38637940285', 'Beatriz Tavares Moura', 3, '0daf8e854444410ec2c51749dc09c1f24ee270a133ae02b28f216d4c5d171ebb', 'Coordenadora', '2025-03-12', NULL, NULL),
  ('2024108699', '65423511674', 'Rafael Nunes Carvalho', 1, '1f82edaedbcd9ebcbe30ad1ef0060cc06d2394e35da9378035a3ad1bf93ca800', 'Apoio logístico', '2024-03-08', 'Coordenou a Infraestrutura em 2024 e 2025; em 2026 passou a apoiar a nova coordenação.', NULL),
  ('2025109922', '55940781691', 'Júlia Fernandes Barros', 2, '9984271b915f197a2be61408d6fcc4d1532725beab1cfb921e90aa22e9f22b23', 'Vice-presidente e revisora', '2025-03-12', NULL, NULL),
  ('2025111220', '84959310367', 'Thiago Monteiro Paiva', 1, '3ee787f7091301a01c5d4a1faf95bdf38822299e7215e2cf80a21fa2c6210f55', 'Coordenador', '2025-03-12', NULL, NULL),
  ('2025112547', '13164752555', 'Camila Rezende Lopes', 4, 'b1e80a400cf4ad44f970e35cc20e74504acbb8b2c0241d28ca4b224edc21ae06', 'Tesoureira', '2025-03-12', NULL, NULL),
  ('2026113855', '34192832704', 'Pedro Augusto Sampaio', 3, '178ac9079e92dce2a8b645d3261bd57ef6c8d2207c864c330826de63ae0bc795', 'Coordenador', '2026-03-09', NULL, NULL),
  ('2026115158', '48350305673', 'Larissa Cunha Figueiredo', 8, '100ec9a80a5fdd8dc21ed8aba28502cd3959a1ed7be99d713124b08cf1e4a104', 'Coordenadora', '2026-03-09', NULL, NULL),
  ('2026116444', '13953767275', 'Gustavo Pereira Lima', 1, 'eeb2e113e0cfa5cfd35333d403c1953e6dba5d3c47b208b22f7b6412c7a43064', 'Suporte técnico', '2026-03-09', 'Afastado de 2026-05-04 a 2026-05-22 por estágio obrigatório.', NULL),
  ('2026117743', '23884969692', 'Ana Clara Duarte Silva', 2, 'd6c5d9db305e3c4cdc3f24231b02fa06a83b863378ae6c5e956a87422a64fb28', 'Designer', '2026-03-09', NULL, NULL),
  ('2026119056', '32871012261', 'Vinícius Moreira Castro', 5, '6fca2bec847802aac511505dcf3b6be08bb14c56b370f954e6745627fa0096b0', 'Emissão de certificados', '2026-03-09', NULL, NULL),
  ('2026120397', '69166978491', 'Isabela Martins Correia', 9, 'da926ea0a0b721d74c26a532f0b8130a23b1aae04f41f03d673b498f450a8c9c', 'Captação de patrocínio', '2026-03-09', NULL, NULL),
  ('2026121680', '01845146220', 'Felipe Arantes Guimarães', 6, '006401a9d9fcd8cdf0a60c402bc8a3a7e964f454deea8f88c829dbc31d84cbd9', 'Apoio logístico', '2026-04-15', 'Substituiu Caio Mendes Fontana, que se formou em 2026.', NULL),
  ('2017122968', '04828148965', 'Eduardo Siqueira Prado', 1, 'ecde92aafbb10ff34a80ee677d2e86189d471d6ba32a3c90d4ecc004dac39b9d', 'Coordenador', '2017-03-06', NULL, NULL),
  ('2017124235', '25288095736', 'Renata Oliveira Batista', 2, 'e37811678e81d3ab4755f15639f3260e483ccf8fb1beebf69af16656b45eacbc', 'Coordenadora', '2017-03-06', NULL, NULL),
  ('2017125512', '15430391115', 'Diego Ramos Teixeira', 1, 'a66cc629f720453c5a3b5c4ad42e1c70378728d102075edc5d3a072443705aa5', 'Coordenador', '2017-03-06', NULL, NULL),
  ('2018126872', '18227824811', 'Patrícia Gomes Andrade', 3, 'cbba0f77a7773c15b324af8eb7e975d2e746ea5b921badf4ee3cf7a78ac09aab', 'Coordenadora', '2018-03-05', NULL, NULL),
  ('2017128187', '63834657832', 'Marcelo Costa Brandão', 1, '319a3f39df8a5606a05f68f27ffbde5c798e1c458e253cf277c6ae766a46d6b2', 'Coordenador', '2017-02-20', NULL, NULL),
  ('2017129467', '13315098310', 'Aline Souza Machado', 2, '628b2c2091c00885253a25d5b04f1845113662f95b70e4cecaf1476e32c6a4fe', 'Coordenadora', '2017-02-20', NULL, NULL),
  ('2021130785', '30103105107', 'Bruno Leite Cardoso', 7, 'f4ec2dcb244455041637dd38bd6f2c96ea95d29b0484a79fb1643c6b791c1733', 'Coordenador', '2021-03-08', NULL, NULL),
  ('2021132075', '34738299786', 'Fernanda Ribeiro Azevedo', 1, '2a9487c6b90475f2c5fe5750c1b2e04531b966cfeaa79d7ec350f086062f052d', 'Coordenadora', '2021-03-08', NULL, NULL),
  ('2022133341', '76311656612', 'Caio Mendes Fontana', 3, '27b52e68173079419e49171eb8d922b9f31fbb77c0db3001eb015e9a069b8c53', 'Coordenador', '2022-03-07', 'Desligado em 2026-04 após a formatura.', NULL),
  ('2023134669', '01065133383', 'Letícia Borges Queiroz', 4, '8c5d8268d21cce0cae23602e38fc2fae867ad2aff6546bba7c1560f625ad5989', 'Coordenadora', '2023-03-06', NULL, NULL);

-- integrante_telefone (32 registros)
INSERT INTO integrante_telefone (rga, telefone) VALUES
  ('2023104791', '67985703737'),
  ('2023104791', '6733538999'),
  ('2025106085', '67990346424'),
  ('2025107381', '67989382113'),
  ('2024108699', '67995869000'),
  ('2024108699', '6734616463'),
  ('2025109922', '67982697462'),
  ('2025111220', '67984129379'),
  ('2025112547', '67986580474'),
  ('2025112547', '6733409248'),
  ('2026113855', '67997151708'),
  ('2026115158', '67994456679'),
  ('2026116444', '67986524305'),
  ('2026116444', '6733179056'),
  ('2026117743', '67994100380'),
  ('2026119056', '67996267547'),
  ('2026120397', '67995193775'),
  ('2026120397', '6734662122'),
  ('2026121680', '67986194119'),
  ('2017122968', '67990955862'),
  ('2017124235', '67982962373'),
  ('2017124235', '6734858516'),
  ('2017125512', '67991522831'),
  ('2018126872', '67982682497'),
  ('2017128187', '67997872858'),
  ('2017128187', '6734455408'),
  ('2017129467', '67982908561'),
  ('2021130785', '67987235251'),
  ('2021132075', '67983280388'),
  ('2021132075', '6731973033'),
  ('2022133341', '67985022727'),
  ('2023134669', '67982333508');

-- integrante_email (36 registros)
INSERT INTO integrante_email (rga, email) VALUES
  ('2023104791', 'mariana.rocha791@academico.ufgd.edu.br'),
  ('2023104791', 'marianaarocha@gmail.com'),
  ('2025106085', 'lucas.vasconcelos85@academico.ufgd.edu.br'),
  ('2025107381', 'beatriz.moura381@academico.ufgd.edu.br'),
  ('2025107381', 'beatriztmoura@gmail.com'),
  ('2024108699', 'rafael.carvalho699@academico.ufgd.edu.br'),
  ('2025109922', 'julia.barros922@academico.ufgd.edu.br'),
  ('2025109922', 'juliafbarros@gmail.com'),
  ('2025111220', 'thiago.paiva220@academico.ufgd.edu.br'),
  ('2025112547', 'camila.lopes547@academico.ufgd.edu.br'),
  ('2025112547', 'camilarlopes@gmail.com'),
  ('2026113855', 'pedro.sampaio855@academico.ufgd.edu.br'),
  ('2026115158', 'larissa.figueiredo158@academico.ufgd.edu.br'),
  ('2026115158', 'larissacfigueiredo@gmail.com'),
  ('2026116444', 'gustavo.lima444@academico.ufgd.edu.br'),
  ('2026117743', 'ana.silva743@academico.ufgd.edu.br'),
  ('2026117743', 'anacsilva@gmail.com'),
  ('2026119056', 'vinicius.castro56@academico.ufgd.edu.br'),
  ('2026120397', 'isabela.correia397@academico.ufgd.edu.br'),
  ('2026120397', 'isabelamcorreia@gmail.com'),
  ('2026121680', 'felipe.guimaraes680@academico.ufgd.edu.br'),
  ('2017122968', 'eduardo.prado968@academico.ufgd.edu.br'),
  ('2017122968', 'eduardosprado@gmail.com'),
  ('2017124235', 'renata.batista235@academico.ufgd.edu.br'),
  ('2017125512', 'diego.teixeira512@academico.ufgd.edu.br'),
  ('2017125512', 'diegorteixeira@gmail.com'),
  ('2018126872', 'patricia.andrade872@academico.ufgd.edu.br'),
  ('2017128187', 'marcelo.brandao187@academico.ufgd.edu.br'),
  ('2017128187', 'marcelocbrandao@gmail.com'),
  ('2017129467', 'aline.machado467@academico.ufgd.edu.br'),
  ('2021130785', 'bruno.cardoso785@academico.ufgd.edu.br'),
  ('2021130785', 'brunolcardoso@gmail.com'),
  ('2021132075', 'fernanda.azevedo75@academico.ufgd.edu.br'),
  ('2022133341', 'caio.fontana341@academico.ufgd.edu.br'),
  ('2022133341', 'caiomfontana@gmail.com'),
  ('2023134669', 'leticia.queiroz669@academico.ufgd.edu.br');

-- coordenadoria (34 registros)
INSERT INTO coordenadoria (id_coordenadoria, nome, descricao_atuacao, id_comissao, rga_coordenador, data_inicio_coordenacao, data_termino_coordenacao) VALUES
  (11, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 1, '2017122968', '2017-03-15', '2017-11-30'),
  (12, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 1, '2017124235', '2017-03-15', '2017-11-30'),
  (13, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 1, '2017125512', '2017-03-15', '2017-11-30'),
  (21, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 2, '2017122968', '2018-03-15', '2018-11-30'),
  (22, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 2, '2017124235', '2018-03-15', '2018-11-30'),
  (23, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 2, '2018126872', '2018-03-15', '2018-11-30'),
  (31, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 3, '2017122968', '2019-03-15', '2019-11-30'),
  (32, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 3, '2017128187', '2019-03-15', '2019-11-30'),
  (33, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 3, '2018126872', '2019-03-15', '2019-11-30'),
  (41, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 4, '2017128187', '2020-03-15', '2020-11-30'),
  (42, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 4, '2017129467', '2020-03-15', '2020-11-30'),
  (43, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 4, '2017125512', '2020-03-15', '2020-11-30'),
  (51, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 5, '2021130785', '2021-03-15', '2021-11-30'),
  (52, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 5, '2017129467', '2021-03-15', '2021-11-30'),
  (53, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 5, '2021132075', '2021-03-15', '2021-11-30'),
  (61, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 6, '2021130785', '2022-03-15', '2022-11-30'),
  (62, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 6, '2022133341', '2022-03-15', '2022-11-30'),
  (63, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 6, '2021132075', '2022-03-15', '2022-11-30'),
  (71, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 7, '2022133341', '2023-03-15', '2023-11-30'),
  (72, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 7, '2023134669', '2023-03-15', '2023-11-30'),
  (73, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 7, '2023104791', '2023-03-15', '2023-11-30'),
  (81, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 8, '2024108699', '2024-03-15', '2024-11-30'),
  (82, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 8, '2023134669', '2024-03-15', '2024-11-30'),
  (83, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 8, '2023104791', '2024-03-15', '2024-11-30'),
  (91, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 9, '2024108699', '2025-03-15', '2025-11-30'),
  (92, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 9, '2025106085', '2025-03-15', '2025-11-30'),
  (93, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 9, '2025107381', '2025-03-15', '2025-11-30'),
  (94, 'Programação Científica', 'Definição das atividades, convite a ministrantes e montagem da grade.', 9, '2025111220', '2025-03-15', '2025-11-30'),
  (95, 'Financeiro', 'Orçamento, captação e prestação de contas dos patrocínios e das inscrições.', 9, '2025112547', '2025-03-15', '2025-11-30'),
  (101, 'Infraestrutura', 'Reserva de espaços, montagem de salas, equipamentos, internet e transmissão.', 10, '2026113855', '2026-03-15', NULL),
  (102, 'Marketing', 'Identidade visual, redes sociais, divulgação nos cursos e contato com a imprensa.', 10, '2025106085', '2026-03-15', NULL),
  (103, 'Certificados', 'Controle de presença, emissão e envio dos certificados de participantes e ministrantes.', 10, '2025107381', '2026-03-15', NULL),
  (104, 'Programação Científica', 'Definição das atividades, convite a ministrantes e montagem da grade.', 10, '2025111220', '2026-03-15', NULL),
  (105, 'Financeiro', 'Orçamento, captação e prestação de contas dos patrocínios e das inscrições.', 10, '2026115158', '2026-03-15', NULL);

-- Vincula cada integrante à coordenadoria em que atua (ciclo integrante <-> coordenadoria)
UPDATE integrante SET id_coordenadoria = 102 WHERE rga = '2025106085';
UPDATE integrante SET id_coordenadoria = 103 WHERE rga = '2025107381';
UPDATE integrante SET id_coordenadoria = 101 WHERE rga = '2024108699';
UPDATE integrante SET id_coordenadoria = 104 WHERE rga = '2025109922';
UPDATE integrante SET id_coordenadoria = 104 WHERE rga = '2025111220';
UPDATE integrante SET id_coordenadoria = 105 WHERE rga = '2025112547';
UPDATE integrante SET id_coordenadoria = 101 WHERE rga = '2026113855';
UPDATE integrante SET id_coordenadoria = 105 WHERE rga = '2026115158';
UPDATE integrante SET id_coordenadoria = 101 WHERE rga = '2026116444';
UPDATE integrante SET id_coordenadoria = 102 WHERE rga = '2026117743';
UPDATE integrante SET id_coordenadoria = 103 WHERE rga = '2026119056';
UPDATE integrante SET id_coordenadoria = 105 WHERE rga = '2026120397';
UPDATE integrante SET id_coordenadoria = 101 WHERE rga = '2026121680';
UPDATE integrante SET id_coordenadoria = 31 WHERE rga = '2017122968';
UPDATE integrante SET id_coordenadoria = 22 WHERE rga = '2017124235';
UPDATE integrante SET id_coordenadoria = 43 WHERE rga = '2017125512';
UPDATE integrante SET id_coordenadoria = 33 WHERE rga = '2018126872';
UPDATE integrante SET id_coordenadoria = 41 WHERE rga = '2017128187';
UPDATE integrante SET id_coordenadoria = 52 WHERE rga = '2017129467';
UPDATE integrante SET id_coordenadoria = 61 WHERE rga = '2021130785';
UPDATE integrante SET id_coordenadoria = 63 WHERE rga = '2021132075';
UPDATE integrante SET id_coordenadoria = 71 WHERE rga = '2022133341';
UPDATE integrante SET id_coordenadoria = 82 WHERE rga = '2023134669';

-- presidencia (10 registros)
INSERT INTO presidencia (id_comissao, rga_presidente, rga_vice) VALUES
  (1, '2017128187', '2017129467'),
  (2, '2017128187', '2017125512'),
  (3, '2017124235', '2017125512'),
  (4, '2017122968', '2018126872'),
  (5, '2017122968', '2017124235'),
  (6, '2017129467', '2017128187'),
  (7, '2021132075', '2021130785'),
  (8, '2022133341', '2021132075'),
  (9, '2023134669', '2023104791'),
  (10, '2023104791', '2025109922');

-- tarefa (14 registros)
INSERT INTO tarefa (id_tarefa, descricao, data_criacao, prazo_final, status, rga_criador) VALUES
  (1, 'Reservar o anfiteatro da FACET para a abertura e o encerramento.', '2025-08-04', '2025-08-29', 'concluida', '2024108699'),
  (2, 'Testar a transmissão ao vivo das palestras da edição 2025.', '2025-09-01', '2025-10-10', 'concluida', '2024108699'),
  (3, 'Levantar o inventário de projetores e caixas de som dos laboratórios.', '2026-08-03', '2026-08-28', 'concluida', '2026113855'),
  (4, 'Configurar a rede Wi-Fi dos laboratórios para os minicursos.', '2026-08-17', '2026-10-09', 'em_andamento', '2026113855'),
  (5, 'Montar a sinalização dos blocos e do auditório.', '2026-09-14', '2026-10-16', 'pendente', '2026113855'),
  (6, 'Criar a identidade visual da 10ª edição.', '2026-06-01', '2026-07-15', 'concluida', '2025106085'),
  (7, 'Publicar a grade de atividades no Instagram.', '2026-09-01', '2026-09-25', 'concluida', '2025106085'),
  (8, 'Produzir os vídeos de divulgação dos ministrantes.', '2026-09-10', '2026-10-12', 'em_andamento', '2025106085'),
  (9, 'Preparar o modelo do certificado com QR Code de validação.', '2026-08-10', '2026-09-30', 'em_andamento', '2025107381'),
  (10, 'Organizar a lista de presença digital por atividade.', '2026-09-01', '2026-10-15', 'pendente', '2025107381'),
  (11, 'Confirmar datas e horários com todos os ministrantes.', '2026-07-01', '2026-08-31', 'concluida', '2025111220'),
  (12, 'Emitir os recibos das cotas de patrocínio.', '2026-08-01', '2026-09-30', 'em_andamento', '2026115158'),
  (13, 'Enviar a prestação de contas da edição 2025 à direção da FACET.', '2025-11-03', '2025-12-12', 'concluida', '2025112547'),
  (14, 'Negociar a cota de coffee break com a cafeteria parceira.', '2026-08-20', '2026-09-18', 'cancelada', '2026115158');

-- tarefa_responsavel (19 registros)
INSERT INTO tarefa_responsavel (id_tarefa, rga) VALUES
  (1, '2024108699'),
  (2, '2024108699'),
  (3, '2026116444'),
  (3, '2026121680'),
  (4, '2026116444'),
  (5, '2026121680'),
  (5, '2024108699'),
  (6, '2026117743'),
  (7, '2026117743'),
  (7, '2025109922'),
  (8, '2026117743'),
  (9, '2026119056'),
  (10, '2026119056'),
  (10, '2025109922'),
  (11, '2025109922'),
  (12, '2026120397'),
  (12, '2025112547'),
  (13, '2025112547'),
  (14, '2026120397');

-- patrocinador (10 registros)
INSERT INTO patrocinador (nome, observacoes) VALUES
  ('Pantanal Software Ltda', 'Empresa de desenvolvimento de sistemas de Dourados; exige logotipo no telão.'),
  ('Guaicurus Telecom', 'Provedor de internet; fornece link dedicado durante o evento.'),
  ('Cooperativa de Crédito Vale do Ivinhema', NULL),
  ('Gráfica Dourados Print', 'Imprime crachás e banners em troca de divulgação.'),
  ('Café Ponta Porã', 'Fornece o coffee break das palestras.'),
  ('Instituto Horizonte de Tecnologia', 'Instituto de pesquisa parceiro da FACET.'),
  ('DataSul Cloud', 'Oferece créditos de nuvem para os minicursos.'),
  ('Livraria Campus Dourados', 'Doa livros para sorteio entre os participantes.'),
  ('Nexo Robótica Educacional', 'Empresta kits de robótica para os workshops.'),
  ('Cerrado Energia Solar', NULL);

-- patrocinio (19 registros)
INSERT INTO patrocinio (nome_patrocinador, id_comissao, tipo_patrocinio, descricao_patrocinio, valor, requisitos) VALUES
  ('Pantanal Software Ltda', 7, 'financeiro', 'Cota ouro', 3000.0, 'Logotipo no telão e nos certificados.'),
  ('Pantanal Software Ltda', 8, 'financeiro', 'Cota ouro', 3500.0, 'Logotipo no telão e nos certificados.'),
  ('Pantanal Software Ltda', 9, 'financeiro', 'Cota ouro', 4000.0, 'Logotipo no telão, nos certificados e estande no hall.'),
  ('Pantanal Software Ltda', 10, 'financeiro', 'Cota diamante', 5000.0, 'Palestra de 20 minutos na abertura e estande no hall.'),
  ('Guaicurus Telecom', 9, 'servico', 'Link de internet dedicado de 500 Mbps', NULL, 'Citação na abertura.'),
  ('Guaicurus Telecom', 10, 'servico', 'Link de internet dedicado de 1 Gbps', NULL, 'Citação na abertura e banner no auditório.'),
  ('Cooperativa de Crédito Vale do Ivinhema', 8, 'financeiro', 'Cota prata', 1500.0, 'Logotipo no site.'),
  ('Cooperativa de Crédito Vale do Ivinhema', 10, 'financeiro', 'Cota prata', 2000.0, 'Logotipo no site e nas redes sociais.'),
  ('Gráfica Dourados Print', 9, 'material', '300 crachás e 6 banners', NULL, 'Logotipo nos crachás.'),
  ('Gráfica Dourados Print', 10, 'material', '400 crachás, 8 banners e 50 cartazes', NULL, 'Logotipo nos crachás.'),
  ('Café Ponta Porã', 10, 'servico', 'Coffee break para 5 dias', NULL, 'Mesa de degustação no hall.'),
  ('Instituto Horizonte de Tecnologia', 6, 'financeiro', 'Cota bronze', 800.0, NULL),
  ('Instituto Horizonte de Tecnologia', 9, 'financeiro', 'Cota prata', 1500.0, 'Logotipo no site.'),
  ('DataSul Cloud', 9, 'servico', 'Créditos de nuvem para 3 minicursos', NULL, 'Uso da plataforma nos minicursos.'),
  ('DataSul Cloud', 10, 'servico', 'Créditos de nuvem para 4 minicursos', NULL, 'Uso da plataforma nos minicursos.'),
  ('Livraria Campus Dourados', 10, 'material', '20 livros técnicos para sorteio', NULL, 'Divulgação do sorteio nas redes sociais.'),
  ('Nexo Robótica Educacional', 10, 'equipamento', '15 kits Arduino para o workshop de robótica', NULL, 'Devolução dos kits ao fim do evento.'),
  ('Cerrado Energia Solar', 8, 'financeiro', 'Cota bronze', 1000.0, NULL),
  ('Cerrado Energia Solar', 10, 'equipamento', 'Painel solar demonstrativo para o hall', NULL, 'Espaço de 4 m² no hall.');

-- participante (25 registros)
INSERT INTO participante (cpf, nome_completo, email, senha_hash, pais, data_nascimento, status_conta, tipo_participante, instituicao_origem) VALUES
  ('91699854300', 'Helena Vasques Tanaka', 'helena.tanaka1@outlook.com', 'f932cb944b0c4073e35c5e5ff08aab351093690f3a99f6df0c1204a953b31ad6', 'Brasil', '1981-04-12', 'ativo', 'docente', 'Universidade Federal da Grande Dourados'),
  ('46247510780', 'Roberto Almeida Freitas', 'roberto.freitas2@gmail.com', '49faa857481aafc489504172d4793025a8e350d173cee5bccb5c36d788de3aad', 'Brasil', '1976-09-03', 'ativo', 'docente', 'Universidade Estadual de Mato Grosso do Sul'),
  ('11838425110', 'Carolina Pires Menezes', 'carolina.menezes3@gmail.com', 'c2cf25dd7fef11bf86a604a71122b3c8f3cfbe6e54019e69fec93ca3d3a9f115', 'Brasil', '1990-01-22', 'ativo', 'externo', 'Pantanal Software Ltda'),
  ('54278498071', 'Sergio Villalba Benítez', 'sergio.benitez4@outlook.com', '80f40de215ad5b3aefdbc8a7a56a068af55da1c7d821adad4c63e3430fbcb920', 'Paraguai', '1979-07-30', 'ativo', 'docente', 'Universidad Nacional de Asunción'),
  ('84124118201', 'Paula Regina Holanda', 'paula.holanda5@gmail.com', '376bfb237e9b4a71c96004a3fbfa9aff58ce713c105b4cb9050c24af1b743e26', 'Brasil', '1985-11-18', 'ativo', 'docente', 'Universidade Federal de Mato Grosso do Sul'),
  ('93534874048', 'André Luiz Quintana', 'andre.quintana6@gmail.com', 'e2b9edc12adaffcb850168eb4ddc9de6cd01560ba33961dea69e041d7284308d', 'Brasil', '1988-05-09', 'ativo', 'externo', 'DataSul Cloud'),
  ('64005242740', 'Natália Coutinho Reis', 'natalia.reis7@outlook.com', 'e864f11d3eefbbaa7a82ff55cb610abc8894f3eec651559f7022dd7fad32aa09', 'Brasil', '1983-02-27', 'ativo', 'docente', 'Instituto Federal de Mato Grosso do Sul'),
  ('68011280511', 'Marcos Vinícius Ortega', 'marcos.ortega8@gmail.com', 'd1371ff49de29f6053fa03103a3dd0ba0379cfbf33ccf593604356537eba7c8b', 'Brasil', '1992-08-14', 'ativo', 'externo', 'Nexo Robótica Educacional'),
  ('26204505351', 'Tatiane Rocha Lemos', 'tatiane.lemos9@gmail.com', '1a724ce4ac2f16ac9952e4483dccacc6fd98581b36265a927ba9b7e29eb4952b', 'Brasil', '1987-12-05', 'ativo', 'docente', 'Universidade Federal da Grande Dourados'),
  ('15869232260', 'Jorge Henrique Salles', 'jorge.salles10@outlook.com', 'd53b4797d1042457f2d538b334cfe188867e12d9c42051f0bd59a752a82cedbd', 'Brasil', '1984-03-16', 'ativo', 'externo', 'Guaicurus Telecom'),
  ('02563421640', 'Gabriela Nascimento Prado', 'gabriela.prado11@gmail.com', '3a5d2adacbd6444296bdea5928ea23d2646b28e2c79d6758bd76ef2591103d16', 'Brasil', '2004-06-21', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('73375433018', 'João Pedro Farias Leal', 'joao.leal12@gmail.com', '37d10dd2487cdec2e15768b19add0f4de2d45424f9c2cbf14d5db9a4c2ce5a25', 'Brasil', '2003-10-02', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('65414586853', 'Maria Eduarda Couto Ramos', 'maria.ramos13@outlook.com', '0504ff1ea3ce9556eeef3972bde424ded128edb4bcb41208f162025aac992fcd', 'Brasil', '2005-01-15', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('01429401940', 'Luiz Fernando Bastos', 'luiz.bastos14@gmail.com', 'c96b15113834e309fe2d288b076e384fe9e9d913da8dad4f5a4e6e9bbd83b293', 'Brasil', '2002-04-30', 'ativo', 'discente', 'Universidade Estadual de Mato Grosso do Sul'),
  ('55698169330', 'Sofia Andrade Pinheiro', 'sofia.pinheiro15@gmail.com', '59dc09a1a2a87b9cf6bec008b50b622c9a4b993f2eb08bb2051f6aec8ce2c8b1', 'Brasil', '2004-09-11', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('06088356170', 'Matheus Cavalcanti Rios', 'matheus.rios16@outlook.com', 'b71c3c48cfe274c1778b99469d28a4883d03c1ff1645d5f389d4d243a4dcc029', 'Brasil', '2003-12-19', 'ativo', 'discente', 'Instituto Federal de Mato Grosso do Sul'),
  ('95148465670', 'Lorena Gimenez Acosta', 'lorena.acosta17@gmail.com', 'd581c22713d11650b22a9db8aea224f5dc2c2b7efdd6d34fde32faa27f05b2d4', 'Paraguai', '2002-07-07', 'ativo', 'discente', 'Universidad Nacional de Asunción'),
  ('48236629970', 'Enzo Gabriel Toledo', 'enzo.toledo18@gmail.com', 'bfef90c92e3750efbd253eef12cd7108b99f1dd0578efcab0eb39f07a9579fcf', 'Brasil', '2006-02-03', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('68044369970', 'Yasmin Carvalho Torres', 'yasmin.torres19@outlook.com', '584e88004a0e38ac1a30dc3cafda20a998722b4625fd210eea4cf7a3f2bc1702', 'Brasil', '2005-05-25', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('77738721472', 'Otávio Brito Medeiros', 'otavio.medeiros20@gmail.com', '204ea089ebb3f86f87f61349960cb23d4d5c2b7138e15bcb9917b6fc0dc4f4ce', 'Brasil', '1972-08-08', 'ativo', 'docente', 'Universidade Federal da Grande Dourados'),
  ('95134332002', 'Clara Luísa Fagundes', 'clara.fagundes21@gmail.com', '1386b08854fd44258b95ebcc32335afb88c78f9a050783e527a93b9071eb4141', 'Brasil', '1995-03-29', 'inativo', 'externo', 'Prefeitura Municipal de Dourados'),
  ('37917693678', 'Henrique Dias Moraes', 'henrique.moraes22@outlook.com', '0c5100e3c6572d64d948b9272b5da59b984ae5a47ff1cc60ad847e664e375d8b', 'Brasil', '2004-11-13', 'ativo', 'discente', 'Universidade Federal da Grande Dourados'),
  ('63201632830', 'Bianca Serra Valadares', 'bianca.valadares23@gmail.com', '385c9181cf6f7225729f45cc99cf88166dd52b3251c38cdf3736d445e577e82b', 'Brasil', '2003-01-31', 'ativo', 'discente', 'Universidade Federal de Mato Grosso do Sul'),
  ('08317278883', 'Rodrigo Tavares Nogueira', 'rodrigo.nogueira24@gmail.com', 'edf6c1159cb4e04f734ad4e35ca31b852d1b96d02e66d1d2e9c4ee8305f9fb93', 'Brasil', '1991-10-20', 'bloqueado', 'externo', 'Cooperativa de Crédito Vale do Ivinhema'),
  ('79868727723', 'Vitória Mello Assunção', 'vitoria.assuncao25@outlook.com', '8faf32180ca51063f569eedfa40d1325525ddd01570f910d3f973dc3ca7d8fa4', 'Brasil', '2005-08-17', 'ativo', 'discente', 'Universidade Federal da Grande Dourados');

-- participante_telefone (29 registros)
INSERT INTO participante_telefone (cpf, telefone) VALUES
  ('91699854300', '67991542261'),
  ('91699854300', '6733500560'),
  ('46247510780', '67999890931'),
  ('11838425110', '67989196824'),
  ('54278498071', '595981800305'),
  ('84124118201', '67990138489'),
  ('84124118201', '6733590885'),
  ('93534874048', '67984097022'),
  ('64005242740', '67999511323'),
  ('68011280511', '67999539665'),
  ('26204505351', '67989372828'),
  ('26204505351', '6734398660'),
  ('15869232260', '67994834304'),
  ('02563421640', '67982299382'),
  ('73375433018', '67987480360'),
  ('65414586853', '67992111545'),
  ('65414586853', '6734969723'),
  ('01429401940', '67995581540'),
  ('55698169330', '67989547069'),
  ('06088356170', '67992079674'),
  ('95148465670', '595981831076'),
  ('48236629970', '67991098202'),
  ('68044369970', '67991812889'),
  ('77738721472', '67998295699'),
  ('95134332002', '67982550675'),
  ('37917693678', '67997588545'),
  ('63201632830', '67996587707'),
  ('08317278883', '67991646752'),
  ('79868727723', '67989697025');

-- ministrante (10 registros)
INSERT INTO ministrante (cpf, imagem_divulgacao) VALUES
  ('91699854300', 'img/ministrantes/helena-tanaka.jpg'),
  ('46247510780', 'img/ministrantes/roberto-freitas.jpg'),
  ('11838425110', 'img/ministrantes/carolina-menezes.jpg'),
  ('54278498071', 'img/ministrantes/sergio-benitez.jpg'),
  ('84124118201', 'img/ministrantes/paula-holanda.jpg'),
  ('93534874048', 'img/ministrantes/andre-quintana.jpg'),
  ('64005242740', 'img/ministrantes/natalia-reis.jpg'),
  ('68011280511', 'img/ministrantes/marcos-ortega.jpg'),
  ('26204505351', 'img/ministrantes/tatiane-lemos.jpg'),
  ('15869232260', NULL);

-- ministrante_certificado (14 registros)
INSERT INTO ministrante_certificado (cpf, certificado) VALUES
  ('91699854300', 'Doutorado em Ciência da Computação - UNICAMP'),
  ('91699854300', 'Mestrado em Ciência da Computação - UFMS'),
  ('46247510780', 'Doutorado em Engenharia Elétrica - USP'),
  ('11838425110', 'AWS Certified Solutions Architect - Associate'),
  ('11838425110', 'Professional Scrum Master I'),
  ('54278498071', 'Doctorado en Ciencias de la Computación - UNA'),
  ('84124118201', 'Doutorado em Estatística - UFSCar'),
  ('93534874048', 'Google Professional Cloud Architect'),
  ('93534874048', 'Certified Kubernetes Administrator (CKA)'),
  ('64005242740', 'Mestrado em Segurança da Informação - UnB'),
  ('64005242740', 'CompTIA Security+'),
  ('68011280511', 'Especialização em Robótica Educacional - IFMS'),
  ('26204505351', 'Doutorado em Computação Aplicada - UFRGS'),
  ('15869232260', 'Cisco CCNA');

-- local_evento (10 registros)
INSERT INTO local_evento (id_local, tipo, faculdade, bloco, numero, capacidade, observacoes) VALUES
  (1, 'auditorio', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'AUD-01', 220, 'Anfiteatro principal, com palco e cabine de som.'),
  (2, 'laboratorio', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'LAB-101', 40, NULL),
  (3, 'laboratorio', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'LAB-102', 40, NULL),
  (4, 'laboratorio', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'LAB-205', 30, 'Laboratório de redes e hardware.'),
  (5, 'sala', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'SL-110', 60, NULL),
  (6, 'sala', 'Faculdade de Ciências Exatas e Tecnologia (FACET)', 'FACET', 'SL-112', 50, NULL),
  (7, 'auditorio', 'Faculdade de Engenharia (FAEN)', 'FAEN', 'AUD-02', 150, NULL),
  (8, 'laboratorio', 'Faculdade de Engenharia (FAEN)', 'FAEN', 'LAB-ROB', 25, 'Laboratório de robótica e automação.'),
  (9, 'sala', 'Faculdade de Administração, Ciências Contábeis e Economia (FACE)', 'FACE', 'SL-203', 70, NULL),
  (10, 'auditorio', 'Reitoria da UFGD', 'Central', 'AUD-CENT', 400, 'Usado apenas na abertura.');

-- local_equipamento (28 registros)
INSERT INTO local_equipamento (id_local, equipamento) VALUES
  (1, 'projetor'),
  (1, 'sistema de som'),
  (1, 'microfone sem fio'),
  (1, 'telão'),
  (2, 'projetor'),
  (2, '40 computadores'),
  (2, 'ar-condicionado'),
  (3, 'projetor'),
  (3, '40 computadores'),
  (3, 'ar-condicionado'),
  (4, 'projetor'),
  (4, 'roteadores e switches'),
  (4, '30 computadores'),
  (5, 'projetor'),
  (5, 'lousa digital'),
  (6, 'projetor'),
  (7, 'projetor'),
  (7, 'sistema de som'),
  (7, 'microfone sem fio'),
  (8, 'bancadas de eletrônica'),
  (8, '25 computadores'),
  (8, 'impressora 3D'),
  (9, 'projetor'),
  (9, 'sistema de som'),
  (10, 'projetor'),
  (10, 'sistema de som'),
  (10, 'telão'),
  (10, 'cabine de tradução');

-- atividade (12 registros)
INSERT INTO atividade (id_atividade, nome, descricao, tipo, duracao_minutos, data_realizacao, vagas, valor_inscricao, status, id_local, id_coordenadoria) VALUES
  (1, 'Inteligência Artificial Generativa no Agronegócio', 'Palestra de abertura sobre usos de IA generativa nas cadeias produtivas de MS.', 'palestra', 90, '2025-10-20 19:00:00', 200, 0.0, 'realizada', 1, 94),
  (2, 'Introdução ao Docker e Kubernetes', 'Minicurso prático de contêineres e orquestração com créditos de nuvem.', 'minicurso', 240, '2025-10-21 13:30:00', 40, 20.0, 'realizada', 2, 94),
  (3, 'Segurança em Redes Domésticas', 'Workshop de configuração segura de roteadores e redes Wi-Fi.', 'workshop', 180, '2025-10-22 08:00:00', 30, 10.0, 'realizada', 4, 94),
  (4, 'Computação Quântica para Iniciantes', 'Palestra sobre qubits, portas quânticas e aplicações atuais.', 'palestra', 60, '2025-10-23 19:00:00', 150, 0.0, 'cancelada', 7, 94),
  (5, 'O Futuro da Computação na Região da Grande Dourados', 'Palestra de abertura com pesquisadores e empresas da região.', 'palestra', 90, '2026-10-19 19:00:00', 380, 0.0, 'confirmada', 10, 104),
  (6, 'Desenvolvimento Web com React', 'Minicurso de criação de interfaces com React e consumo de APIs.', 'minicurso', 240, '2026-10-20 13:30:00', 40, 25.0, 'confirmada', 2, 104),
  (7, 'Ciência de Dados com Python', 'Minicurso de pandas, visualização e modelos simples de regressão.', 'minicurso', 240, '2026-10-20 13:30:00', 40, 25.0, 'confirmada', 3, 104),
  (8, 'Robótica com Arduino', 'Workshop de montagem e programação de robôs seguidores de linha.', 'workshop', 180, '2026-10-21 08:00:00', 25, 15.0, 'confirmada', 8, 104),
  (9, 'Redes Neurais na Prática', 'Minicurso de redes neurais com PyTorch aplicado a imagens.', 'minicurso', 240, '2026-10-21 13:30:00', 40, 25.0, 'confirmada', 3, 104),
  (10, 'Integração Acadêmica Brasil-Paraguai em TI', 'Palestra sobre cooperação e mobilidade acadêmica na fronteira.', 'palestra', 60, '2026-10-22 19:00:00', 150, 0.0, 'confirmada', 7, 104),
  (11, 'Infraestrutura em Nuvem para Startups', 'Palestra sobre custos, escalabilidade e arquitetura em nuvem.', 'palestra', 60, '2026-10-22 10:00:00', 60, 0.0, 'planejada', 5, 104),
  (12, 'Montagem e Manutenção de Redes', 'Workshop de cabeamento estruturado e configuração de switches.', 'workshop', 180, '2026-10-23 08:00:00', 30, 10.0, 'planejada', 4, 101);

-- ministra (15 registros)
INSERT INTO ministra (cpf_ministrante, id_atividade) VALUES
  ('91699854300', 1),
  ('93534874048', 2),
  ('64005242740', 3),
  ('15869232260', 3),
  ('46247510780', 4),
  ('91699854300', 5),
  ('11838425110', 5),
  ('11838425110', 6),
  ('84124118201', 7),
  ('68011280511', 8),
  ('26204505351', 9),
  ('54278498071', 10),
  ('93534874048', 11),
  ('15869232260', 12),
  ('64005242740', 12);

-- inscricao (52 registros)
INSERT INTO inscricao (cpf, id_atividade, data_inscricao, presenca) VALUES
  ('02563421640', 1, '2025-09-01 09:00:00', TRUE),
  ('73375433018', 1, '2025-09-02 10:07:00', TRUE),
  ('65414586853', 1, '2025-09-03 11:14:00', TRUE),
  ('01429401940', 1, '2025-09-04 12:21:00', FALSE),
  ('55698169330', 1, '2025-09-05 13:28:00', TRUE),
  ('06088356170', 1, '2025-09-06 14:35:00', TRUE),
  ('95148465670', 1, '2025-09-07 15:42:00', TRUE),
  ('77738721472', 1, '2025-09-08 16:49:00', FALSE),
  ('95134332002', 1, '2025-09-09 17:56:00', TRUE),
  ('08317278883', 1, '2025-09-10 18:03:00', TRUE),
  ('02563421640', 2, '2025-09-01 09:00:00', TRUE),
  ('73375433018', 2, '2025-09-02 10:07:00', TRUE),
  ('01429401940', 2, '2025-09-03 11:14:00', TRUE),
  ('06088356170', 2, '2025-09-04 12:21:00', FALSE),
  ('37917693678', 2, '2025-09-05 13:28:00', TRUE),
  ('65414586853', 3, '2025-09-01 09:00:00', TRUE),
  ('55698169330', 3, '2025-09-02 10:07:00', TRUE),
  ('95148465670', 3, '2025-09-03 11:14:00', TRUE),
  ('68044369970', 3, '2025-09-04 12:21:00', FALSE),
  ('02563421640', 5, '2026-09-01 09:00:00', FALSE),
  ('73375433018', 5, '2026-09-02 10:07:00', FALSE),
  ('65414586853', 5, '2026-09-03 11:14:00', FALSE),
  ('01429401940', 5, '2026-09-04 12:21:00', FALSE),
  ('55698169330', 5, '2026-09-05 13:28:00', FALSE),
  ('06088356170', 5, '2026-09-06 14:35:00', FALSE),
  ('48236629970', 5, '2026-09-07 15:42:00', FALSE),
  ('68044369970', 5, '2026-09-08 16:49:00', FALSE),
  ('77738721472', 5, '2026-09-09 17:56:00', FALSE),
  ('37917693678', 5, '2026-09-10 18:03:00', FALSE),
  ('63201632830', 5, '2026-09-11 09:10:00', FALSE),
  ('79868727723', 5, '2026-09-12 10:17:00', FALSE),
  ('02563421640', 6, '2026-09-01 09:00:00', FALSE),
  ('65414586853', 6, '2026-09-02 10:07:00', FALSE),
  ('55698169330', 6, '2026-09-03 11:14:00', FALSE),
  ('37917693678', 6, '2026-09-04 12:21:00', FALSE),
  ('73375433018', 7, '2026-09-01 09:00:00', FALSE),
  ('01429401940', 7, '2026-09-02 10:07:00', FALSE),
  ('06088356170', 7, '2026-09-03 11:14:00', FALSE),
  ('68044369970', 7, '2026-09-04 12:21:00', FALSE),
  ('79868727723', 7, '2026-09-05 13:28:00', FALSE),
  ('48236629970', 8, '2026-09-01 09:00:00', FALSE),
  ('68044369970', 8, '2026-09-02 10:07:00', FALSE),
  ('37917693678', 8, '2026-09-03 11:14:00', FALSE),
  ('02563421640', 9, '2026-09-01 09:00:00', FALSE),
  ('06088356170', 9, '2026-09-02 10:07:00', FALSE),
  ('37917693678', 9, '2026-09-03 11:14:00', FALSE),
  ('63201632830', 9, '2026-09-04 12:21:00', FALSE),
  ('95148465670', 10, '2026-09-01 09:00:00', FALSE),
  ('77738721472', 10, '2026-09-02 10:07:00', FALSE),
  ('63201632830', 10, '2026-09-03 11:14:00', FALSE),
  ('54278498071', 11, '2026-09-01 09:00:00', FALSE),
  ('01429401940', 11, '2026-09-02 10:07:00', FALSE);


-- Script para Indexing: índices secundários para otimizar as consultas frequentes



-- Busca de participantes pelo nome (portal, credenciamento, certificados)
CREATE INDEX idx_participante_nome ON participante (nome_completo);

-- Relatórios de público por tipo (discente/docente/externo) e instituição
CREATE INDEX idx_participante_tipo_instituicao
  ON participante (tipo_participante, instituicao_origem);

-- Busca de integrantes da organização pelo nome
CREATE INDEX idx_integrante_nome ON integrante (nome_completo);

-- Grade do evento: atividades ordenadas/filtradas por data
CREATE INDEX idx_atividade_data ON atividade (data_realizacao);

-- Filtro de atividades por tipo e status (ex.: minicursos confirmados)
CREATE INDEX idx_atividade_tipo_status ON atividade (tipo, status);

-- Painel dos coordenadores: tarefas por status e prazo
CREATE INDEX idx_tarefa_status_prazo ON tarefa (status, prazo_final);

-- Busca de coordenadorias pelo nome ao longo das edições
CREATE INDEX idx_coordenadoria_nome ON coordenadoria (nome);

-- Relatório de patrocínios por tipo
CREATE INDEX idx_patrocinio_tipo ON patrocinio (tipo_patrocinio);

-- Conferência dos índices criados
