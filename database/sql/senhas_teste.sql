-- =====================================================================
-- Senhas conhecidas para os usuários de teste (para a apresentação).
--
-- As senhas originais dos dados de teste são desconhecidas (só o hash está no banco).
-- Este script grava uma senha conhecida usando a função SHA2(senha, 256) do próprio
-- MySQL: o resultado é o mesmo SHA-256 hexadecimal que o sistema usa (App\Auth\Sha256Hasher),
-- então funciona mesmo em hospedagens sem acesso ao "php artisan".
--
-- Senha de TODOS os usuários abaixo:  semana2026
--
-- Rode DEPOIS do script.sql (ou do script_deploy.sql). Não altera nenhuma estrutura.
-- Em hospedagem compartilhada, apague a linha "USE" se o banco tiver outro nome.
-- =====================================================================

USE semana_academica;

-- ---------------- Painel da organização (login por RGA) ----------------
-- Presidente da 10ª edição (2026)
UPDATE integrante SET senha_hash = SHA2('semana2026', 256) WHERE rga = '2023104791'; -- Mariana Albuquerque Rocha
-- Vice-presidente da 10ª edição
UPDATE integrante SET senha_hash = SHA2('semana2026', 256) WHERE rga = '2025109922'; -- Júlia Fernandes Barros
-- Coordenador de Infraestrutura na 10ª edição
UPDATE integrante SET senha_hash = SHA2('semana2026', 256) WHERE rga = '2026113855'; -- Pedro Augusto Sampaio
-- Integrante comum (Suporte técnico, atua em Infraestrutura, responsável por tarefas)
UPDATE integrante SET senha_hash = SHA2('semana2026', 256) WHERE rga = '2026116444'; -- Gustavo Pereira Lima

-- ---------------- Portal do participante (login por e-mail) ----------------
-- Participante ATIVO com presença em atividades de 2025 (tem certificados)
UPDATE participante SET senha_hash = SHA2('semana2026', 256) WHERE email = 'gabriela.prado11@gmail.com';   -- Gabriela Nascimento Prado
-- Segundo participante ativo (útil para testar lotação de vagas)
UPDATE participante SET senha_hash = SHA2('semana2026', 256) WHERE email = 'joao.leal12@gmail.com';        -- João Pedro Farias Leal
-- Participante BLOQUEADO (demonstra que o login é recusado)
UPDATE participante SET senha_hash = SHA2('semana2026', 256) WHERE email = 'rodrigo.nogueira24@gmail.com'; -- Rodrigo Tavares Nogueira
-- Participante INATIVO (também não consegue entrar)
UPDATE participante SET senha_hash = SHA2('semana2026', 256) WHERE email = 'clara.fagundes21@gmail.com';   -- Clara Luísa Fagundes

-- Conferência
SELECT rga AS login, nome_completo, 'integrante' AS perfil FROM integrante
 WHERE senha_hash = SHA2('semana2026', 256)
UNION ALL
SELECT email, nome_completo, CONCAT('participante (', status_conta, ')') FROM participante
 WHERE senha_hash = SHA2('semana2026', 256);
